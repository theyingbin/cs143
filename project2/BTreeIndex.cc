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
	
	if (errorCode != 0)
		return errorCode;

	/*

	I don't think this section is needed

	// Check that indexname is opened for the first time
	// Do we even need this?
	if (pf.endPid() == 0) {
		rootPid = -1;
		treeHeight = 0;

		// check that it is opened for writing if first time
		// I don't think this is needed...
		// 
		// errorCode = pf.write(0, buffer);
		// if(errorCode != 0)
		// 	return errorCode;

		return 0;
	}

	*/


	// Does it make sense to store rootPid and treeHeight in the 0th Disk Page?

	errorCode = pf.read(0, buffer);

	if (errorCode != 0)
		return errorCode;

	// Is this use of temps to validate the values necessary?
	// I feel like if we store this info on the 0th disk page, then there could be garbage there on initial open
	// So maybe handle that up at the pf.endPid() == 0 check?

	PageId tempRootPid;
	int tempTreeHeight;

  	memcpy(buffer, &tempRootPid, sizeof(PageId));
	memcpy(buffer + sizeof(PageId), &tempTreeHeight, sizeof(int));

	// Can't be 0 bc 0 is holding rootPid, treeHeight
	if (tempRootPid > 0 && tempTreeHeight >= 0) {
		rootPid = tempRootPid;
		treeHeight = tempTreeHeight;
	}

	/*
	memcpy(buffer, &rootPid, sizeof(PageId));
	memcpy(buffer + sizeof(PageId), &treeHeight, sizeof(int));
	*/

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

	if (errorCode != 0)
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
	// Probably do this recursively to keep track of parent too?
    return 0;
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
		if (errorCode != 0)
			return errorCode;

		// Get the child pointer we want to follow
		errorCode = nonLeafNode.locateChildPtr(searchKey, pid);
		if (errorCode != 0)
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

	PageId cursorPid = cursor.pid;
	int cursorEid = cursor.eid;

	// 0 not allowed bc store rootPid and treeHeight there
	if(cursorPid <= 0 || cursorEid < 0)
		return RC_INVALID_CURSOR;

	BTLeafNode leafNode;
	// Get leaf node content
	errorCode = leafNode.read(cursorPid, pf);
	if (errorCode != 0) 
		return errorCode;

	// Get entry info
	errorCode = leafNode.readEntry(cursorEid, key, rid);
	if (errorCode != 0) 
		return errorCode;

	// Make sure to account for overflow
	if (cursorEid + 1 >= leafNode.getKeyCount()) {
		cursorEid = 0;
		cursorPid = leafNode.getNextNodePtr();
	} else {
		cursorEid++;
	}

	cursor.eid = cursorEid;
	cursor.pid = cursorPid;

    return 0;
}
