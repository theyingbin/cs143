#include "BTreeIndex.h"
#include <iostream>
#include <cstdio>
#include <climits>
using namespace std;


// int main() {
//     int rc;
//     BTreeIndex tree;
//     rc = tree.open("xlarge.idx", 'w');

//     IndexCursor cursor;
//     rc = tree.locate(0, cursor);
//     int key;
//     RecordId rid;
//     for(int i = 0; i < 12278; i++) {
//         rc = tree.readForward(cursor, key, rid);
//         cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
//     }
//     rc = tree.close();
// }

int main() {
    int rc;
    BTreeIndex tree;
    rc = tree.open("test.txt", 'w');
    for(int i = 0; i < 4; i++) {
    	int key = (i+1)*3;
    	RecordId rid;
    	rid.pid = i + 1;
    	rid.sid = i + 2;
    	rc = tree.insert(key, rid);
    }
    RecordId rid2;
    rid2.pid = 7;
    rid2.sid = 8;
    RecordId rid3;
    rid3.pid = 8;
    rid3.sid = 9;
    tree.insert(7, rid2);
    tree.insert(8, rid3);

    for(int i=0; i<10; i++){
        int key = i+20;
        RecordId rid;
        rid.pid = i + 21;
        rid.sid = i+22;
        tree.insert(key, rid);
    }

    IndexCursor cursor;
    rc = tree.locate(INT_MIN, cursor);
    int key;
    RecordId rid;
    for(int i = 0; i < 16; i++) {
    	rc = tree.readForward(cursor, key, rid);
    	cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
    }
    rc = tree.close();

    BTreeIndex new_tree;
    rc = new_tree.open("test.txt", 'w');
    rc = new_tree.locate(0, cursor);
    for(int i = 0; i < 16; i++) {
    	rc = new_tree.readForward(cursor, key, rid);
    	cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
    }
    rc = new_tree.close();
    rc = remove("test.txt");
}

// int main() {
//     int rc;
//     BTreeIndex tree;
//     rc = tree.open("test.txt", 'w');
//     for(int i = 0; i < 5; i++) {
//      int key = i + 0;
//      RecordId rid;
//      rid.pid = i + 1;
//      rid.sid = i + 2;
//      rc = tree.insert(key, rid);
//     }
//     tree.print_path(4);

// }