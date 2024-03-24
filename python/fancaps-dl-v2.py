import re
import os
import argparse
import urllib.request
import concurrent.futures
from bs4 import BeautifulSoup

parser = argparse.ArgumentParser()
parser.add_argument('url', nargs='?', help='Url')
parser.add_argument('--output', type=str, default="Downloads", help='Path of folder')
args = parser.parse_args()

def getEpLinks(url):
    links = []
    pageUrl = url
    pageNumber = 1
    name = None

    while pageUrl:
        request = urllib.request.Request(pageUrl, headers={'User-Agent': 'Mozilla/5.0'})
        page = urllib.request.urlopen(request)
        beautifulSoup = BeautifulSoup(page, 'html.parser')

        for link in beautifulSoup.find_all("a", href=re.compile("^.*?/episodeimages.php\?")):
            href = 'https://fancaps.net' + link.get('href')
            if href:
                match = re.search(r"https://fancaps.net/.*?/episodeimages.php\?\d+-(.*?)/", href)
                if match:
                    if not name:
                        name = match.group(1)
                    if name == match.group(1):
                        links.append(href)
        if beautifulSoup.find("a", title="Next Page"):
            pageNumber += 1
            pageUrl = url + f"&page={pageNumber}"
        else:
            pageUrl = None  
    links= list(set(links))
    links.sort()
    return links

def getPicLinks(epUrl):
    links = []
    pageUrl = epUrl
    pageNumber = 1
    epType = None
    alt = None
    cdn = None

    match = re.search(r"https://fancaps.net/(.*?)/(.*)", epUrl)
    epType = match.group(1)
    nextUrl = match.group(2)

    if epType == 'movie':
        cdn = 'mvcdn'
    elif epType == 'tv':
        cdn = 'tvcdn'
    else:
        cdn = 'ancdn'
    

    while pageUrl:
        request = urllib.request.Request(pageUrl, headers={'User-Agent': 'Mozilla/5.0'})
        page = urllib.request.urlopen(request)
        beautifulSoup = BeautifulSoup(page, "html.parser")

        for img in beautifulSoup.find_all("img", src=re.compile("^https://"+epType+"thumbs.fancaps.net/")):
            imgSrc = img.get("src")
            imgAlt = img.get("alt")
            if not alt:
                alt = imgAlt
            if alt == imgAlt:
                links.append(imgSrc.replace("https://"+epType+"thumbs.fancaps.net/", "https://"+cdn+".fancaps.net/"))
        next = nextUrl+f"&page={pageNumber + 1}"
        nextPage = beautifulSoup.find("a", href=next)
        if nextPage:
            pageNumber += 1
            pageUrl = epUrl + f"&page={pageNumber}"
        else:
            pageUrl = None
    
    return links
    
def downloadFile(url, filename):
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req) as response, open(filename, 'wb') as out_file:
        data = response.read()
        out_file.write(data)
    print(f"\t {url} Downloaded")

print('todo movie')

if args.url:
    for epUrl in getEpLinks(args.url):
        print(f"Current URL scrapping: {epUrl}")

        match = re.search(r"https://fancaps.net/.*?/episodeimages.php\?\d+-([^/]+)/([^/]+)", epUrl)
        if match:
            subfolder = os.path.join(args.output, match.group(1) + "/" + match.group(2))
        else:
            subfolder = args.output

        if not os.path.exists(subfolder):
            os.makedirs(subfolder)
    
        if not os.path.exists(subfolder):
            os.makedirs(subfolder)
            
        print(f"\t Folder {subfolder} created")
        for picUrl in getPicLinks(epUrl):
            with concurrent.futures.ThreadPoolExecutor(max_workers=12) as executor:
                filename = os.path.join(subfolder, picUrl.split('/')[-1])
                if not os.path.exists(filename):
                    executor.submit(downloadFile, picUrl, filename)
