import argparse
import concurrent.futures
import re
import os
import urllib.request
from bs4 import BeautifulSoup

parser = argparse.ArgumentParser()
parser.add_argument('url', nargs='?', help='Url')
parser.add_argument('--urls', type=str, default=None, help='Path for txt file who contain URLS (1 url per line)')
parser.add_argument('--output', type=str, default="Downloads", help='Path of folder')
args = parser.parse_args()

def getEpLinks(url):
    links = []
    pageUrl = url
    pageNumber = 1
    animeName = None

    while pageUrl:
        request = urllib.request.Request(pageUrl, headers={'User-Agent': 'Mozilla/5.0'})
        page = urllib.request.urlopen(request)
        beautifulSoup = BeautifulSoup(page, "html.parser")

        for link in beautifulSoup.find_all("a", href=re.compile("^https://fancaps.net/anime/episodeimages.php\?")):
            href = link.get("href")
            if href:
                match = re.search(r"https://fancaps.net/anime/episodeimages.php\?\d+-(.*?)/", href)
                if match:
                    if not animeName:
                        animeName = match.group(1)
                    if animeName == match.group(1):
                        links.append(href)
        nexTPage = beautifulSoup.find("a", title="Next Page")
        if nexTPage:
            pageNumber += 1
            pageUrl = url + f"&page={pageNumber}"
        else:
            pageUrl = None    
    return links

def getPicLinks(url):
    links = []
    pageUrl = url
    pageNumber = 1
    alt = None

    match = re.search("^https://fancaps.net/anime/(.*)", url)
    nextUrl = match.group(1)

    while pageUrl:
        request = urllib.request.Request(pageUrl, headers={'User-Agent': 'Mozilla/5.0'})
        page = urllib.request.urlopen(request)
        beautifulSoup = BeautifulSoup(page, "html.parser")

        for img in beautifulSoup.find_all("img", src=re.compile("^https://animethumbs.fancaps.net/")):
            imgAlt = img.get("alt")
            imgSrc = img.get("src")
            if not alt:
                alt = imgAlt
            if alt == imgAlt:
                links.append(imgSrc.replace("https://animethumbs.fancaps.net/", "https://ancdn.fancaps.net/"))

        next = nextUrl+f"&page={pageNumber + 1}"
        nextPage = beautifulSoup.find("a", href=next)
        if nextPage:
            pageNumber += 1
            pageUrl = url + f"&page={pageNumber}"
        else:
            pageUrl = None 
    return links

def downloadFile(url, filename):
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req) as response, open(filename, 'wb') as out_file:
        data = response.read()
        out_file.write(data)
    print(f"{url} Downloaded")

if args.url:
    if re.search(r"https://fancaps.net/anime/episodeimages.php\?\d+-.*/Episode_\d+", args.url):
        args.urls = [args.url]
    if re.search(r"https://fancaps.net/anime/showimages.php?", args.url):
        args.urls = getEpLinks(args.url)

if args.urls:
    for epLink in args.urls:
        print(f"Current URL scrapping: {epLink}")
        for picLink in getPicLinks(epLink):
            with concurrent.futures.ThreadPoolExecutor(max_workers=4) as executor:
                match = re.search(r"https://fancaps.net/anime/episodeimages.php\?\d+-([^/]+)/([^/]+)", epLink)
                if match:
                    subfolder = os.path.join(args.output, match.group(1) + "_" + match.group(2))
                else:
                    subfolder = args.output

                if not os.path.exists(subfolder):
                    os.makedirs(subfolder)

                filename = os.path.join(subfolder, picLink.split('/')[-1])
                executor.submit(downloadFile, picLink, filename)

 
