/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include <climits>
#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "BTreeIndex.h"

using namespace std;

// external functions and variables for load file and sql command parsing 
extern FILE* sqlin;
int sqlparse(void);


RC SqlEngine::run(FILE* commandline)
{
  fprintf(stdout, "Bruinbase> ");

  // set the command line input and start parsing user input
  sqlin = commandline;
  sqlparse();  // sqlparse() is defined in SqlParser.tab.c generated from
               // SqlParser.y by bison (bison is GNU equivalent of yacc)

  return 0;
}

RC SqlEngine::select(int attr, const string& table, const vector<SelCond>& cond)
{
  RecordFile rf;
  RecordId   rid;  
  RC     rc;

  if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
    fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
    return rc;
  }

  int    key;     
  string value;
  int    count;
  int    diff;
  bool usesValue = false;

  int condValue;
  int keyToEqual = INT_MAX;
  // minKeyInRange and maxKeyInRange are inclusive. If their value is 42, then 42 is a valid option for the search  
  int minKeyInRange = INT_MAX;
  int maxKeyInRange = INT_MIN;
  BTreeIndex btIndex;
  IndexCursor indexCursor;
  bool openedIndex = false;
  bool atLeastOneCondition = false;
  SelCond selCond;


  for (int i = 0; i < cond.size(); i++) {
    selCond = cond[i];
    condValue = atoi(selCond.value);

    if (selCond.attr == 1 && selCond.comp != SelCond::NE) {
      // Key
      atLeastOneCondition = true;
      if (selCond.comp == SelCond::EQ) {
        keyToEqual = condValue;
        if (maxKeyInRange == INT_MIN || condValue <= maxKeyInRange)
          maxKeyInRange = condValue;
        if (minKeyInRange == INT_MAX || condValue >= minKeyInRange)
          minKeyInRange = condValue; 

      } else if (selCond.comp == SelCond::LT) {
        if (maxKeyInRange == INT_MIN || condValue <= maxKeyInRange)
          maxKeyInRange = condValue - 1;

      } else if (selCond.comp == SelCond::LE) {
        if (maxKeyInRange == INT_MIN || condValue <= maxKeyInRange)
          maxKeyInRange = condValue;
        
      } else if (selCond.comp == SelCond::GT) {
        if (minKeyInRange == INT_MAX || condValue >= minKeyInRange)
          minKeyInRange = condValue + 1;
        
      } else if (selCond.comp == SelCond::GE) {
        if (minKeyInRange == INT_MAX || condValue >= minKeyInRange)
          minKeyInRange = condValue; 
      }
    }
    else if(selCond.attr == 2){
      usesValue = true;
    }
  }


  // Valid range for keys does not exist, so don't even try
  if (!(maxKeyInRange != INT_MIN && minKeyInRange != INT_MAX && maxKeyInRange < minKeyInRange)) {

    if (btIndex.open(table + ".idx", 'r') != 0 || (!atLeastOneCondition && attr != 4)) {
      rid.pid = rid.sid = 0;
      count = 0;
      while (rid < rf.endRid()) {
        if ((rc = rf.read(rid, key, value)) < 0) {
          fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
          goto exit_select;
        }

        for (unsigned i = 0; i < cond.size(); i++) {
          switch (cond[i].attr) {
            case 1:
              diff = key - atoi(cond[i].value);
              break;
            case 2:
              diff = strcmp(value.c_str(), cond[i].value);
              break;
          }

          switch (cond[i].comp) {
            case SelCond::EQ:
              if (diff != 0) goto next;
              break;
            case SelCond::NE:
              if (diff == 0) goto next;
              break;
            case SelCond::GT:
              if (diff <= 0) goto next;
              break;
            case SelCond::LT:
              if (diff >= 0) goto next;
              break;
            case SelCond::GE:
              if (diff < 0) goto next;
              break;
            case SelCond::LE:
              if (diff > 0) goto next;
              break;
          }

        }

        count++;

        switch (attr) {
        case 1:  // SELECT key
          fprintf(stdout, "%d\n", key);
          break;
        case 2:  // SELECT value
          fprintf(stdout, "%s\n", value.c_str());
          break;
        case 3:  // SELECT *
          fprintf(stdout, "%d '%s'\n", key, value.c_str());
          break;
        }

        // move to the next tuple
        next:
        ++rid;
      }
    } 
    else {
      // Use index case. This should never occur when have some equality statement on value. Only works for key
      openedIndex = true;
      count = 0;
      if (keyToEqual != INT_MAX) 
        btIndex.locate(keyToEqual, indexCursor);
      else if (minKeyInRange != INT_MAX)
        btIndex.locate(minKeyInRange, indexCursor);
      else
        btIndex.locate(INT_MIN, indexCursor);

      while(btIndex.readForward(indexCursor, key, rid) == 0){
        if(attr == 4 && !usesValue){
          if(keyToEqual != INT_MAX && key != keyToEqual){
            // breaks out of while loop since the conditionss are no longer true
            break;
          }
          if(maxKeyInRange != INT_MIN && key > maxKeyInRange){
            break;
          }
          if(minKeyInRange != INT_MAX && key < minKeyInRange){
            break;
          }
          count++;
          continue;
        }
        if((rc = rf.read(rid, key, value)) < 0){
          break;
        }
        if(keyToEqual != INT_MAX && key != keyToEqual){
          // breaks out of while loop since the conditionss are no longer true
          break;
        }
        if(maxKeyInRange != INT_MIN && key > maxKeyInRange){
          break;
        }
        if(minKeyInRange != INT_MAX && key < minKeyInRange){
          break;
        }
        // check the conditions on the tuple
        for (unsigned i = 0; i < cond.size(); i++) {
          // compute the difference between the tuple value and the condition value
          switch (cond[i].attr) {
            case 1:
              diff = key - atoi(cond[i].value);
              break;
            case 2:
              diff = strcmp(value.c_str(), cond[i].value);
              break;
          }

          // skip the tuple if any condition is not met
          switch (cond[i].comp) {
            case SelCond::EQ:
              if (diff != 0) {
                if (cond[i].attr == 1)
                  goto end_early;
                else
                  goto keep_going;
              }              
              break;
            case SelCond::NE:
              if (diff == 0) goto keep_going;
              break;
            case SelCond::GT:
              if (diff <= 0) goto keep_going;
              break;
            case SelCond::LT:
              if (diff >= 0) {
                if (cond[i].attr == 1)
                  goto end_early;
                else
                  goto keep_going;
              }
              break;
            case SelCond::GE:
              if (diff < 0) goto keep_going;
              break;
            case SelCond::LE:
              if (diff > 0) {
                if (cond[i].attr == 1)
                  goto end_early;
                else
                  goto keep_going;
              }
              break;
          }
        }
        count++;
   
        // print the tuple 
        switch (attr) {
        case 1:  // SELECT key
          cout << key << endl;
          break;
        case 2:  // SELECT value
          cout << value << endl;
          break;
        case 3:  // SELECT *
          cout << key << " '" << value << "'" << endl;
          break;
        }
        keep_going: ;
      }
    }
  }
  end_early:

  if (attr == 4) {
    cout << count << endl;
  }
  rc = 0;

  exit_select:
  if (openedIndex) btIndex.close();
  rf.close();
  return rc;
}

  

RC SqlEngine::load(const string& table, const string& loadfile, bool index)
{
  /* your code here */
  RecordFile record(table + ".tbl", 'w');
  ifstream input(loadfile.c_str());
  RecordId rid;
  int key;
  string value;
  RC returnCode;
  string loadTuple;
  BTreeIndex btIndex;

  if(input){
    if (index) {
        btIndex.open(table + ".idx", 'w');
        while (getline(input, loadTuple)) {
          if(returnCode = parseLoadLine(loadTuple, key, value)) {
            // badly formatted data
            return returnCode;
          }
          if(returnCode = record.append(key, value, rid)) {
            // cannot append input
            return returnCode;
          }
          if (returnCode = btIndex.insert(key, rid)) {
            return returnCode;
          }
        }
        btIndex.close();
    } else {
      while(getline(input, loadTuple)){
        if(returnCode = parseLoadLine(loadTuple, key, value)){
          // badly formatted data
          return returnCode;
        }
        if(returnCode = record.append(key, value, rid)){
          // cannot append input
          return returnCode;
        }
      }
    }
    record.close();
    input.close();
  }
  else{
    // cannot open file. probably because it doesn't exist or no read permissions
    return RC_FILE_OPEN_FAILED;
  }
  return 0;
}

RC SqlEngine::parseLoadLine(const string& line, int& key, string& value)
{
    const char *s;
    char        c;
    string::size_type loc;
    
    // ignore beginning white spaces
    c = *(s = line.c_str());
    while (c == ' ' || c == '\t') { c = *++s; }

    // get the integer key value
    key = atoi(s);

    // look for comma
    s = strchr(s, ',');
    if (s == NULL) { return RC_INVALID_FILE_FORMAT; }

    // ignore white spaces
    do { c = *++s; } while (c == ' ' || c == '\t');
    
    // if there is nothing left, set the value to empty string
    if (c == 0) { 
        value.erase();
        return 0;
    }

    // is the value field delimited by ' or "?
    if (c == '\'' || c == '"') {
        s++;
    } else {
        c = '\n';
    }

    // get the value string
    value.assign(s);
    loc = value.find(c, 0);
    if (loc != string::npos) { value.erase(loc); }

    return 0;
}
