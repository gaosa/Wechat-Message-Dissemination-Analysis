import networkx as nx
import seaborn as sns
from scipy.stats import pearsonr
from matplotlib import pyplot as plt
from collections import defaultdict
from itertools import groupby

with open("pages.txt") as fin:
	_pages = [x.strip().split(",") for x in fin.readlines()]
	key = lambda p: p[0]
	_pages = sorted(_pages, key=key)
	pages = {}
	for u, p in groupby(_pages, key=key):
		pages[u] = zip(*zip(*p)[1:])
with open("path.txt") as fin:
	path = [x.strip().split(",") for x in fin.readlines()]
with open("share.txt") as fin:
	_share = [x.strip().split(",") for x in fin.readlines()]
	key = lambda s: s[0]
	_share = sorted(_share)
	share = {}
	for u, t in groupby(_share, key=key):
		share[u] = zip(*t)[1]

D = nx.MultiDiGraph()
l = []
s = set()
errors = set()
node2first = defaultdict(lambda: (2**32, 0)) # (receive time, path time)
node2last = defaultdict(lambda: (0, 0))
for u, v, t in path:
	if u in share:
		D.add_edge(u, v, weight=int(t) - int(share[u][0]))
		if int(t) < node2first[v][0]:
			node2first[v] = (int(t), int(t) - int(share[u][0]))
		if int(t) > node2last[v][0]:
			node2last[v] = (int(t), int(t) - int(share[u][0]))
	else:
		D.add_edge(u, v, weight=1000)
		if u not in s:
			l.append(u)
			s.add(u)
		if int(t) < node2first[v][0]:
			node2first[v] = (int(t), 1000)
		if int(t) > node2last[v][0]:
			node2last[v] = (int(t), 1000)
weights = [x[-1] for x in D.edges_iter(data="weight")]
degrees = [D.out_degree(x) for x in D.nodes()]
first_paths = [node2first[x][1] for x in D.nodes()]
times = []
num_pages = []
first_paths2 = []
degrees2 = []
for x in D.nodes():
	if x in pages:
		#times.append(int(pages[x][0][1]) - node2first[x][0])
		time = 2**32
		for p in path:
			if p[1] == x and int(p[2]) <= int(pages[x][0][1]):
				time = min(time, int(pages[x][0][1]) - int(p[2]))
		times.append(time)
		
		num_pages.append(int(pages[x][0][0]))
		degrees2.append(D.out_degree(x))
		first_paths2.append(node2first[x][1])
print "nodes: %d   edges: %d" % (len(D.nodes()), len(D.edges()))
print "average path time: %.2f s" % (float(sum(weights)) / len(weights))
print "min path time: %d s" % min(weights)
print "max path time: %d s" % max(weights)
print "average browse time: %.2f s" % (float(sum(times)) / len(times))
print "max browse time: %d s" % max(times)
print "min browse time: %d s" % min(times)
print "average out degree: %.2f" % (float(sum(degrees)) / len(degrees))
print "min out degree: %d" % min(degrees)
print "max out degree: %d" % max(degrees)
print "average pages browsed: %.2f " % (float(sum([int(pages[x][0][0]) for x in pages])) / len(pages))
print "co-relateness(degree, first path): %.2f" % pearsonr(degrees, first_paths)[0]
print "co-relateness(degree, pages): %.2f" % pearsonr(degrees2, num_pages)[0]
print "co-relateness(first path, pages): %.2f" % pearsonr(first_paths2, num_pages)[0]
print "flow hierarchy: %.2f" % nx.flow_hierarchy(D)

with open("weights.csv", "w") as fout:
	fout.write("\n".join([str(x) for x in weights]))
with open("out_degrees.csv", "w") as fout:
	fout.write("\n".join([str(x) for x in degrees]))
with open("pages.csv", "w") as fout:
	fout.write("\n".join([pages[x][0][0] for x in pages]))
	
sns.set_style("whitegrid")

weights = [w / 3600.0 for w in weights]
plt.hist(weights, bins=50, log=True, edgecolor="grey", lw=1, alpha=0.8)
plt.xlabel("path time (hour)")
plt.ylabel("count")
plt.savefig("weights.png", dpi=300)
plt.clf()

plt.hist(degrees, bins=50, log=True, edgecolor="grey", lw=1, align="left", alpha=0.8)
plt.xlabel("out degree")
plt.ylabel("count")
plt.savefig("out_degrees.png", dpi=300)
plt.clf()

plt.hist([int(pages[x][0][0]) for x in pages], bins=range(9), normed=True, edgecolor="grey", lw=1, align="left", alpha=0.8)
plt.xlabel("pages browsed")
plt.ylabel("count")
plt.gca().yaxis.set_major_formatter(plt.FuncFormatter(lambda y, pos: "%d %%" % (y*100)))
plt.savefig("pages.png", dpi=300)
plt.clf()

nx.draw(D, node_size=150, node_color="cornflowerblue", lw=0.5, alpha=0.8)
plt.show()

D.remove_nodes_from([x for x in D.nodes() if not D.out_degree(x)])
start = 0
size = 1000
color = "cornflowerblue"
pos = nx.spectral_layout(D)
while start != len(l):
	nx.draw_networkx_nodes(D, pos, node_size=size, nodelist=l[start:], node_color=color, alpha=0.8)
	last_len = len(l)
	for i in range(start, last_len):
		for e in D.edges_iter(l[i]):
			if e[1] not in s and D.out_degree(e[1]):
				l.append(e[1])
				s.add(e[1])
	start = last_len
	size *= 0.66
nx.draw_networkx_edges(D, pos)
nx.draw_networkx_nodes(D, pos, node_size=size, nodelist=set(D.nodes()) - set(l), node_color=color, alpha=0.8)
plt.show()