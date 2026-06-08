import argparse
import json
import os
import re
import sys
from pathlib import Path

if sys.platform == "win32":
    if "SYSTEMROOT" not in os.environ:
        os.environ["SYSTEMROOT"] = os.environ.get("WINDIR", r"C:\Windows")
    if "WINDIR" not in os.environ:
        os.environ["WINDIR"] = os.environ["SYSTEMROOT"]

os.environ.setdefault("PYTHONUNBUFFERED", "1")
os.environ.setdefault("PYTHONIOENCODING", "utf-8")

import asyncio

sys.stdout.reconfigure(encoding='utf-8')

from twscrape import API


def extract_tweet_id(url_or_id: str) -> str:
    """URL'den tweet ID'sini cikarir. Direkt ID verilmisse onu dondurur."""
    url_or_id = url_or_id.strip()
    if url_or_id.isdigit():
        return url_or_id
    match = re.search(r'/status(?:es)?/(\d+)', url_or_id)
    if match:
        return match.group(1)
    raise ValueError(f"Tweet ID cikarilamadi: {url_or_id}")


async def main():
    parser = argparse.ArgumentParser(description="Fetch a single tweet by URL or ID via twscrape.")
    parser.add_argument("--url", required=True, help="Tweet URL veya tweet ID")
    parser.add_argument("--accounts-db", default=str(Path(__file__).with_name("accounts.db")))
    args = parser.parse_args()

    api = API(args.accounts_db)

    try:
        tweet_id = extract_tweet_id(args.url)
        tweet = await api.tweet_details(int(tweet_id))

        photos = [p.url for p in tweet.media.photos]
        videos = []
        for v in tweet.media.videos:
            if v.variants:
                best = max(v.variants, key=lambda x: x.bitrate or 0)
                if best.url:
                    videos.append(best.url)

        result = {
            "success": True,
            "tweet": {
                "tweet_id": str(tweet.id),
                "url": tweet.url,
                "text": tweet.rawContent,
                "tweeted_at": tweet.date.isoformat() if tweet.date else None,
                "language": tweet.lang,
                "like_count": tweet.likeCount or 0,
                "retweet_count": tweet.retweetCount or 0,
                "reply_count": tweet.replyCount or 0,
                "view_count": tweet.viewCount or 0,
                "quote_count": tweet.quoteCount or 0,
                "photo_urls": photos,
                "video_urls": videos,
                "links": [link.url for link in tweet.links],
                "user": {
                    "username": tweet.user.username,
                    "display_name": getattr(tweet.user, 'display_name', None) or getattr(tweet.user, 'displayname', None) or '',
                },
            },
        }
    except Exception as e:
        result = {"success": False, "error": str(e)}

    print(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    asyncio.run(main())
