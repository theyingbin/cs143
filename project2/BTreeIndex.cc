/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"
#include <cstdlib>
#include <cstring>
#include <climits>
#include <fstream>
#include <iostream>


using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rootPid = -1;
    treeHeight = 0;
    memset(buffer, 0, PageFile::PAGE_SIZE);
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
    RC errorCode = pf.open(indexname, mode);
    
    if (errorCode)
        return errorCode;


    if (pf.endPid() == 0) {
        return pf.write(0, buffer);
    }

    errorCode = pf.read(0, buffer);

    if (errorCode)
        return errorCode;

    // Use temp values to ensure we get valid values
    PageId tempRootPid;
    int tempTreeHeight;

    memcpy(&tempRootPid, buffer, sizeof(PageId));
    memcpy(&tempTreeHeight, buffer + sizeof(PageId), sizeof(int));

    // Can't be 0 bc 0 is holding rootPid, treeHeight
    if (tempRootPid > 0 && tempTreeHeight >= 0) {
        rootPid = tempRootPid;
        treeHeight = tempTreeHeight;
    }

    return 0;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    memcpy(buffer, &rootPid, sizeof(PageId));
    memcpy(buffer + sizeof(PageId), &treeHeight, sizeof(int));

    RC errorCode = pf.write(0, buffer);

    if (errorCode)
        return errorCode;

    return pf.close();
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
    RC error;

    // Check if we need to create a new tree
    if(treeHeight == 0){
        BTLeafNode node;
        node.insert(key, rid);

        // if endPid == 0, set to rootPid to 1, else set to endPid
        rootPid = pf.endPid() == 0 ? 1 : pf.endPid();
        treeHeight++;
        cerr << "(Top level insert - tree height = 0 --> Key " << key << " inserted at pid " << rootPid << "\n";
        return node.write(rootPid, pf);
    }
    else{
        int insertedKey = -1;
        PageId insertedPid = -1;

        return insertHelper(key, rid, 1, rootPid, insertedKey, insertedPid);
    }

}

RC BTreeIndex::insertHelper(int key, const RecordId& rid, int height, PageId curPid, int& insertedKey, PageId& insertedPid){
    RC ret;

    insertedKey = -1;
    insertedPid = -1;

    // We are at the leaf level
    if(height == treeHeight){

        // Get contents of the leaf we are looking at
        BTLeafNode curLeaf;
        curLeaf.read(curPid, pf);

        // attempt to insert
        ret = curLeaf.insert(key, rid);
                    // fprintf(stderr, "cur next-%d key-%d (outside)\n", curLeaf.getNextNodePtr(), key);
        if(!ret){
            // fprintf(stderr, "%s - %d\n", "Success for insert", key);
            cerr << "Inside Helper, insert into leaf success, no split --> Key " << key << " inserted at pid " << curPid << "\n";
            curLeaf.write(curPid, pf);
            return 0;
        }
        else{
            // node was full so we must do a split
            BTLeafNode sibling;
            int siblingKey;
            ret = curLeaf.insertAndSplit(key, rid, sibling, siblingKey);
            if(ret)
                return ret;

            insertedPid = pf.endPid();
            insertedKey = siblingKey;

            sibling.setNextNodePtr(curLeaf.getNextNodePtr());
            curLeaf.setNextNodePtr(insertedPid);

                    // fprintf(stderr, "sibling next-%d key-%d (inside)\n", sibling.getNextNodePtr(), siblingKey);

            // set changes
            ret = curLeaf.write(curPid, pf);
            if(ret)
                return ret;        
            ret = sibling.write(insertedPid, pf);
            if(ret)
                return ret;

            cerr << "Inside Helper, split required, success --> Key " << key << " inserted\n";
            cerr << "Sibling to above --> Key " << siblingKey << " inserted at pid " << insertedPid << "\n";



            // check for the case in which the insertion requires a new root
            if(treeHeight == 1){
                cerr << "Inside Helper, split required, new root required, root has --> pid1 " << curPid << " key " << insertedKey << " pid2 " << insertedPid << "\n";
                BTNonLeafNode root;
                root.initializeRoot(curPid, insertedKey, insertedPid);
                treeHeight++;

                rootPid = pf.endPid();
                root.write(rootPid, pf);
            }

            return 0;
        }
    }
    else{
        // We are not in the leaf level anymore
        cerr << "Inside Helper, nonleafnode level, success --> Key " << key << " inserted at pid " << curPid << "\n";
        BTNonLeafNode node;
        node.read(curPid, pf);

        PageId childPid = -1;
        node.locateChildPtr(key, childPid);
        // fprintf(stderr, "childPid - %d\n", childPid);

        cerr << "Inside Helper, calling helper again --> Key " << key << " pid to look into " << childPid << "\n";
        ret = insertHelper(key, rid, height+1, childPid, insertedKey, insertedPid);
        // fprintf(stderr, "insert helper for key: %d gave insertedKey: %d insertedPid: %d \n", key, insertedKey, insertedPid);

        if(insertedKey != -1 || insertedPid != -1){
            // a split happened so we need to modify our current node
            ret = node.insert(insertedKey, insertedPid);
            if(ret == 0){
                cerr << "Inside Helper, split occurred, modify current node --> Key " << insertedKey << " inserted at pid " << insertedPid << "\n";
                node.write(curPid, pf);
                return 0;
            }

            // we must also do a split at this level since node is full
            BTNonLeafNode sibling;
            int sibKey;

            node.insertAndSplit(insertedKey, insertedPid, sibling, sibKey);

            PageId sibPid = pf.endPid();
            insertedPid = sibPid;
            insertedKey = sibKey;

            ret = node.write(curPid, pf);
            if(ret)
                return ret;
            ret = sibling.write(sibPid, pf);
            if(ret)
                return ret;

            cerr << "Inside Helper, split occurred, modify current node, needed another split -->" << 
                "current pid " << curPid << " sibling pid " << sibPid << "\n";


            // check for the case in which we need a new root
            if(treeHeight == 1){
                BTNonLeafNode root;
                root.initializeRoot(curPid, sibKey, sibPid);
                treeHeight++;

                rootPid = pf.endPid();
                root.write(rootPid, pf);
            }
        }

        return 0;
    }
}

/**
 * Run the standard B+Tree key search algorithm and identify the
 * leaf node where searchKey may exist. If an index entry with
 * searchKey exists in the leaf node, set IndexCursor to its location
 * (i.e., IndexCursor.pid = PageId of the leaf node, and
 * IndexCursor.eid = the searchKey index entry number.) and return 0.
 * If not, set IndexCursor.pid = PageId of the leaf node and
 * IndexCursor.eid = the index entry immediately after the largest
 * index key that is smaller than searchKey, and return the error
 * code RC_NO_SUCH_RECORD.
 * Using the returned "IndexCursor", you will have to call readForward()
 * to retrieve the actual (key, rid) pair from the index.
 * @param key[IN] the key to find
 * @param cursor[OUT] the cursor pointing to the index entry with
 *                    searchKey or immediately behind the largest key
 *                    smaller than searchKey.
 * @return 0 if searchKey is found. Othewise an error code
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
    RC errorCode;   
    BTNonLeafNode nonLeafNode;
    BTLeafNode leafNode;
    int height = 1;
    PageId pid = rootPid;
    int eid;

    while (height != treeHeight) {
        // Get the non leaf node content
        errorCode = nonLeafNode.read(pid, pf);
        if (errorCode)
            return errorCode;

        // Get the child pointer we want to follow
        errorCode = nonLeafNode.locateChildPtr(searchKey, pid);
        if (errorCode)
            return errorCode;

        height++;
    }

    // Get the leafnode content
    errorCode = leafNode.read(pid, pf);
    if (errorCode != 0)
        return errorCode;

    // Look for the searchKey
    // If no such record, errorcode is set to RC_NO_SUCH_RECORD
    // And eid is set correctly to just after largest element that is smaller (aka right place)
    // so just assign and return either way
    errorCode = leafNode.locate(searchKey, eid);
    cursor.pid = pid;
    cursor.eid = eid;

    return errorCode;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{
    RC errorCode;

    // 0 not allowed bc store rootPid and treeHeight there
    if(cursor.pid <= 0 || cursor.eid < 0)
        return RC_INVALID_CURSOR;

    BTLeafNode leafNode;
    // Get leaf node content
    errorCode = leafNode.read(cursor.pid, pf);
    if (errorCode) 
        return errorCode;

    // Get entry info
    errorCode = leafNode.readEntry(cursor.eid, key, rid);
    if (errorCode) 
        return errorCode;

    // Make sure to account for overflow
    if (cursor.eid + 1 >= leafNode.getKeyCount()) {
        cursor.eid = 0;
        cursor.pid = leafNode.getNextNodePtr();
    } else {
        cursor.eid++;
    }

    return 0;
}
