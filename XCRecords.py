import sys
try:
	filename = sys.argv[1]
except IndexError:
	filename = input("Enter a file of records: ")

fd = open(filename,'r')
previous = 'start'
line = fd.readline()
headers = []
records = []
while(line):#Get the headers
	if(previous.strip() == ''):
		break
	elif line.strip()!='':
		headers.append(line.strip())
	#else:
	previous = line
	line = fd.readline()
"""
while(line):
	newRecord = {}
	if line.strip() != '':
		for i in headers:
			if line.strip()=='':
				break
			newRecord[i] = line.strip()
			line = fd.readline()
		records.append(newRecord)
	else:
		line = fd.readline()
"""	
while(line):
	newRecord = '{'
	if line.strip() != '':
		for i in headers:
			if line.strip()=='':
				break
			newRecord += '"'+i+'":"' + line.strip() + '"'
			line = fd.readline()
			if line.strip()!='' and i!=headers[-1]:
				newRecord += ','
		newRecord += '}'
		records.append(newRecord)
	else:
		line = fd.readline()
#	print(newRecord)
#	input('Press enter to continue')

fd.close()
newarr = '['
for r in records:
	newarr += r
	if r != records[-1]:
		newarr += ','
newarr += ']'

import json, os
#from collections import OrderedDict

newfilename = os.path.basename(os.path.splitext(filename)[0])
fp = open('/Users/ryanbudhu/Documents/Records/XC/jsons/'+newfilename+'.json','w')
fp.write('var '+newfilename+' = ')
#json_records =json.dump(json.loads(newarr,object_pairs_hook=OrderedDict),fp)
fp.write(newarr)
fp.write(';')
fp.close()

#print(json.dumps(records))