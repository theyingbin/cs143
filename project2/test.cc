#include "BTreeIndex.h"
#include <iostream>
using namespace std;

int main() {
    BTreeIndex tree;
    tree.open("test.txt", 'w');
    for(int i = 0; i < 3; i++) {
    	int key = i;
    	RecordId rid;
    	rid.pid = i + 1;
    	rid.sid = i + 2;
    	tree.insert(key, rid);
    }
    IndexCursor cursor;
    tree.locate(0, cursor);
    int key;
    RecordId rid;
    for(int i = 0; i < 3; i++) {
    	tree.readForward(cursor, key, rid);
    	cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
    }
    tree.close();

    BTreeIndex new_tree;
    new_tree.open("test.txt", 'w');
    new_tree.locate(0, cursor);
    for(int i = 0; i < 3; i++) {
    	new_tree.readForward(cursor, key, rid);
    	cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
    }
    new_tree.close();
}
