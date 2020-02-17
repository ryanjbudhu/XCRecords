import sys
if len(sys.argv)>1:
	filename = sys.argv[1]
else:
	filename = input("Enter a filename: ")
try:
	resultsf = open(filename,'r')
except:
	print('File was not found.')
	exit()
line = resultsf.readline()
para = line + '\n'
record = False
while line:
	line = resultsf.readline()
	if 'Results - ' in line:
		record = True
		continue
	if record:
		if '=' in line:
			break
		para += line + '\n'
import re
pattern = r'\n[0-9 ][ 0-9][0-9].*'
raw_matches = re.findall(pattern, para)
matches = [x.lstrip() for x in raw_matches]
results_json = '[\n'
patterns = [(r'^[0-2]?[0-9]?[0-9]','Place'), (r'.+(?=,)','Lastname'), (r'[A-Za-z-]+ [A-Za-z]*','Firstname'), (r'[FSJ][RO]','Year'), (r'[^0-9]+(?![1-4 ])','School'), (r'([0-9]?[0-9]:[0-9][0-9]\.[0-9][0-9]|DN[SF])','Time'), (r'[0-3]?[0-9]?[0-9]','Points')]
for idx in range(len(matches)):
	match = matches[idx]
	results_json+='\t{'
	for pattern,colname in patterns:
		col = re.search(pattern, match)
		if col != None:
			match = match[col.end():]
			col = col.group().rstrip()
			col = col.lstrip()
			if colname != 'Place' and colname!='Points' or col=='--':
				results_json += ',\n\t\t"' + colname + '":"' + col + '"'
			else:
				results_json += '\n\t\t"' + colname + '":' + col + ''
		else:
			col = 'NaN'
		#print(col,end='\t')
	if idx+1<len(matches):
		results_json += '\n\t},\n'
	else:
		results_json += '\n\t}\n'
results_json += ']'
print(results_json)
withoutext = re.search('(.+?)(\.[^.]*$|$)', filename).group(1) + '.json'
#outputf = open(withoutext,'w')
#outputf.write(results_json)