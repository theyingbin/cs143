#include "BTreeIndex.h"
#include <iostream>
#include <cstdio>
using namespace std;


int main() {
    int rc;
    BTreeIndex tree;
    rc = tree.open("xlarge.idx", 'w');

    IndexCursor cursor;
    rc = tree.locate(0, cursor);
    int key;
    RecordId rid;
    for(int i = 0; i < 12278; i++) {
        rc = tree.readForward(cursor, key, rid);
        cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
    }
    rc = tree.close();
}

// int main() {
//     int rc;
//     BTreeIndex tree;
//     rc = tree.open("test.txt", 'w');
//     for(int i = 0; i < 20000; i++) {
//     	int key = i;
//     	RecordId rid;
//     	rid.pid = i + 1;
//     	rid.sid = i + 2;
//     	rc = tree.insert(key, rid);
//     }

//     IndexCursor cursor;
//     rc = tree.locate(0, cursor);
//     int key;
//     RecordId rid;
//     for(int i = 0; i < 20000; i++) {
//     	rc = tree.readForward(cursor, key, rid);
//     	cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
//     }
//     rc = tree.close();

//     BTreeIndex new_tree;
//     rc = new_tree.open("test.txt", 'w');
//     rc = new_tree.locate(0, cursor);
//     for(int i = 0; i < 20000; i++) {
//     	rc = new_tree.readForward(cursor, key, rid);
//     	cout << "key: " << key << "; pid: " << rid.pid << ", sid: " << rid.sid << endl;
//     }
//     rc = new_tree.close();
//     rc = remove("test.txt");
// }

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