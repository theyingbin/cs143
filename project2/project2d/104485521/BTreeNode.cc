#include "BTreeNode.h"
#include <iostream>
#include <cstring>
#include <cstdlib>

using namespace std;

/*
 * Format of Leaf Node:
 * KEY COUNT | rid | key | rid | key | ....... | rid | key | pid
 * I did it this way so it affects as little of our existing code as possible
 */



/* Constructor for the BTLeafNode class */
BTLeafNode::BTLeafNode(){
    memset(buffer, 0, PageFile::PAGE_SIZE);
}


/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf)
{ return pf.read(pid, buffer); }
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf)
{ return pf.write(pid, buffer); }

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTLeafNode::getKeyCount()
{ 
    int* numKeys = (int*)(buffer);
    return *numKeys;
}


void BTLeafNode::setKeyCount(int keys){
    int* numKeys = (int*)(buffer);
    *numKeys = keys;
}
/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTLeafNode::insert(int key, const RecordId& rid)
{ 
    int entrySize = sizeof(RecordId) + sizeof(int);

    int numEntriesAllowed = (PageFile::PAGE_SIZE - sizeof(PageId) - sizeof(int)) / entrySize;

    if(getKeyCount() + 1 > numEntriesAllowed) {
        return RC_NODE_FULL;
    }

    int offset = sizeof(int);
    // We know one more entry can fit so subtract entry size
    // Go through until key is smaller than key in the buffer
    for(int i = 0; i < getKeyCount(); i++){
        int checkKey;
        memcpy(&checkKey, buffer + offset + sizeof(RecordId), sizeof(int));
        if(checkKey >= key)
            break;
        offset += entrySize;
    }

    // Now we know where to put key, rid into
    // Copy buffer up until that point
    char* nextBuffer = (char*) malloc(PageFile::PAGE_SIZE);  
    memset(nextBuffer, 0, PageFile::PAGE_SIZE);
    memcpy(nextBuffer, buffer, offset);

    // Store key and then rid
    memcpy(nextBuffer + offset, &rid, sizeof(RecordId));
    memcpy(nextBuffer + offset + sizeof(RecordId), &key, sizeof(int));

    // After we insert our entry, copy the rest in
    // sizeof(int) + getKeyCount() * entrySize - offset gives entries after insert
    memcpy(nextBuffer + offset + entrySize, buffer + offset, sizeof(int) + getKeyCount() * entrySize - offset);

    // Add in nextNodePtr at the end
    PageId nextNodePtr = getNextNodePtr();
    memcpy(nextBuffer + PageFile::PAGE_SIZE - sizeof(PageId), &nextNodePtr, sizeof(PageId));

    memcpy(buffer, nextBuffer, PageFile::PAGE_SIZE);
    free(nextBuffer);

    setKeyCount(getKeyCount() + 1);

    return 0; 
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey)
{ 
    int entrySize = sizeof(RecordId) + sizeof(int);

    int numEntriesAllowed = (PageFile::PAGE_SIZE - sizeof(PageId) - sizeof(int)) / entrySize;


    if (getKeyCount() < numEntriesAllowed)
        return RC_INVALID_FILE_FORMAT;

    if (sibling.getKeyCount() != 0)
        return RC_INVALID_ATTRIBUTE;

    memset(sibling.buffer, 0, PageFile::PAGE_SIZE);
    int halfKeys = (getKeyCount()+1) / 2;
    int halfIndex = halfKeys * entrySize;

    memcpy(sibling.buffer + sizeof(int), buffer + halfIndex + sizeof(int), PageFile::PAGE_SIZE - sizeof(PageId) - halfIndex - sizeof(int));

    sibling.setKeyCount(getKeyCount() - halfKeys);
    sibling.setNextNodePtr(getNextNodePtr());

    memset(buffer + sizeof(int) + halfIndex, 0, PageFile::PAGE_SIZE - sizeof(PageId) - halfIndex - sizeof(int));
    setKeyCount(halfKeys);

    memcpy(&siblingKey, sibling.buffer + sizeof(int) + sizeof(RecordId), sizeof(int));

    if (key < siblingKey)
        insert(key, rid);
    else
        sibling.insert(key, rid);

    return 0; 
}

/**
 * If searchKey exists in the node, set eid to the index entry
 * with searchKey and return 0. If not, set eid to the index entry
 * immediately after the largest index key that is smaller than searchKey,
 * and return the error code RC_NO_SUCH_RECORD.
 * Remember that keys inside a B+tree node are always kept sorted.
 * @param searchKey[IN] the key to search for.
 * @param eid[OUT] the index entry number with searchKey or immediately
                   behind the largest key smaller than searchKey.
 * @return 0 if searchKey is found. Otherwise return an error code.
 */
RC BTLeafNode::locate(int searchKey, int& eid)
{
    int indexSize = sizeof(RecordId) + sizeof(int);     // gets the size of every pair of RecordId and int
    for(int i=0; i<getKeyCount(); i++){
        int* checkPtr = (int*)(buffer + sizeof(int) + i*indexSize + sizeof(RecordId));

        if(*checkPtr == searchKey){
            eid = i;
            return 0;
        }
        else if(*checkPtr > searchKey){
            eid = i;
            return RC_NO_SUCH_RECORD;
        }
    }

    eid = getKeyCount();
    return RC_NO_SUCH_RECORD;
}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid)
{
    if(eid >= getKeyCount() || eid < 0)
        return RC_NO_SUCH_RECORD;
    
    int indexSize = sizeof(RecordId) + sizeof(int);
    memcpy(&rid, buffer + sizeof(int) + eid * indexSize, sizeof(RecordId));
    memcpy(&key, buffer + sizeof(int) + eid * indexSize + sizeof(RecordId), sizeof(int));
    return 0;       
}

/*
 * Return the pid of the next slibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr()
{ 
    PageId pid = 0;
    memcpy(&pid, buffer + PageFile::PAGE_SIZE - sizeof(PageId), sizeof(PageId));       // gets the last bits of buffer, where it stores the pointer to the next node
    return pid;
}

/*
 * Set the pid of the next slibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid)
{
    if(pid < 0)
        return RC_INVALID_PID;
    
    char* end = buffer + PageFile::PAGE_SIZE - sizeof(PageId);
    memcpy(end, &pid, sizeof(PageId));

    return 0;
}


////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////



/*
 * Format of NonLeaf Node:
 * pid | key | pid | key | ....... | pid | key | pid | KEY COUNT
 * Note there can be space between the last pid and KEY COUNT
 */



/* Constructor for the BTNonLeafNode class */
BTNonLeafNode::BTNonLeafNode(){
    memset(buffer, 0, PageFile::PAGE_SIZE);
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{ return pf.read(pid, buffer); }
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{ return pf.write(pid, buffer); }

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTNonLeafNode::getKeyCount()
{
    int* numKeys = (int*)(buffer + PageFile::PAGE_SIZE - sizeof(int));
    return *numKeys;
}

void BTNonLeafNode::setKeyCount(int keys){
    int* numKeys = (int*)(buffer + PageFile::PAGE_SIZE - sizeof(int));
    *numKeys = keys;
}

/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTNonLeafNode::insert(int key, PageId pid)
{ 
    int entrySize = sizeof(int) + sizeof(PageId);

    int numEntriesAllowed = (PageFile::PAGE_SIZE - sizeof(int) - sizeof(PageId)) / entrySize;

    if(getKeyCount() + 1 > numEntriesAllowed) {
        return RC_NODE_FULL;
    }

    // Offset first pid
    int i = sizeof(PageId);

    // We know one more entry can fit so subtract entry size
    // Go through until key is smaller than key in the buffer
    while (i < getKeyCount() * entrySize + sizeof(PageId)) {
        int tempKey;
        memcpy(&tempKey, buffer + i, sizeof(int));

        if (key <= tempKey)
            break;

        i += entrySize;
    }

    // Now we know where to put key, rid into
    // Copy buffer up until that point
    char* nextBuffer = (char*) malloc(PageFile::PAGE_SIZE);
    memset(nextBuffer, 0, PageFile::PAGE_SIZE);
    memcpy(nextBuffer, buffer, i);

    // Store key and then pid
    memcpy(nextBuffer + i, &key, sizeof(int));
    memcpy(nextBuffer + i + sizeof(int), &pid, sizeof(PageId));

    
    // After we insert our entry, copy the rest in
    // sizeof(PageId) + getKeyCount() * entrySize - i gives entries after insert (ignore initial entry)
    memcpy(nextBuffer + i + entrySize, buffer + i, sizeof(PageId) + getKeyCount() * entrySize - i);

    int keyCount = getKeyCount() + 1;
    memcpy(nextBuffer + PageFile::PAGE_SIZE - sizeof(int), &keyCount, sizeof(int));


    memcpy(buffer, nextBuffer, PageFile::PAGE_SIZE);
    free(nextBuffer);

    return 0; 
}

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{ 
    int entrySize = sizeof(PageId) + sizeof(int);

    int numEntriesAllowed = (PageFile::PAGE_SIZE - sizeof(PageId) - sizeof(int)) / entrySize;

    if (getKeyCount() < numEntriesAllowed)
        return RC_INVALID_FILE_FORMAT;

    if (sibling.getKeyCount() != 0)
        return RC_INVALID_ATTRIBUTE;

    memset(sibling.buffer, 0, PageFile::PAGE_SIZE);

    int halfKeys = (getKeyCount()+1) / 2;
    int halfIndex = halfKeys * entrySize - sizeof(int);

    int lastFirstHalf;
    int firstSecondHalf;

    memcpy(&lastFirstHalf, buffer + halfIndex - entrySize, sizeof(int));
    memcpy(&firstSecondHalf, buffer + halfIndex, sizeof(int));

    if (key < lastFirstHalf) {
        memcpy(sibling.buffer, buffer + halfIndex - sizeof(PageId), PageFile::PAGE_SIZE - halfIndex - sizeof(int) + sizeof(PageId));
        
        sibling.setKeyCount(getKeyCount() - halfKeys);

        midKey = lastFirstHalf;

        memset(buffer + halfIndex - entrySize, 0, PageFile::PAGE_SIZE - halfIndex + entrySize);
        setKeyCount(halfKeys - 1);

        insert(key, pid);

    } else if (key > firstSecondHalf) {
        // First pid, key pair is median
        memcpy(sibling.buffer, buffer + halfIndex + entrySize - sizeof(PageId), getKeyCount() * entrySize + sizeof(PageId) - halfIndex);
        
        sibling.setKeyCount(getKeyCount() - halfKeys);

        midKey = firstSecondHalf;

        memset(buffer + halfIndex, 0, PageFile::PAGE_SIZE - halfIndex);
        setKeyCount(halfKeys - 1);

        sibling.insert(key, pid);

    } else {
        memcpy(sibling.buffer, buffer + halfIndex - sizeof(PageId), PageFile::PAGE_SIZE - halfIndex - sizeof(int) + sizeof(PageId));
        
        sibling.setKeyCount(getKeyCount() - halfKeys);

        midKey = key;

        memset(buffer + halfIndex, 0, PageFile::PAGE_SIZE - halfIndex);
        setKeyCount(halfKeys);

    }

    return 0;
}

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{
    int indexSize = sizeof(PageId) + sizeof(int);
    for(int i=0; i < getKeyCount(); i++){
        // Search algorithm: find the first key greater than the search key and follow it's left pointer
        int* checkKey = (int*)(buffer + i * indexSize + sizeof(PageId));
        if(*checkKey > searchKey){
            memcpy(&pid, buffer + i * indexSize, sizeof(PageId));
            return 0;
        }
    }
    // if we don't find anything, follow the last key's right pointer
    memcpy(&pid, buffer+getKeyCount()*indexSize, sizeof(PageId));
    return 0;
}

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{
    memset(buffer, 0, PageFile::PAGE_SIZE);
    memcpy(buffer, &pid1, sizeof(PageId));
    memcpy(buffer + sizeof(PageId), &key, sizeof(key));
    memcpy(buffer + sizeof(PageId) + sizeof(int), &pid2, sizeof(PageId));
    setKeyCount(1);
    return 0;
}
