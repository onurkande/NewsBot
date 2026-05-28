import argparse
import asyncio
import json
from pathlib import Path

from twscrape import API, gather


def tweet_to_dict(tweet):
    return {
        "tweet_id": str(tweet.id),
        "tweet_url": tweet.url,
        "text": tweet.rawContent,
        "tweeted_at": tweet.date.isoformat() if tweet.date else None,
        "language": tweet.lang,
        "like_count": tweet.likeCount or 0,
        "retweet_count": tweet.retweetCount or 0,
        "reply_count": tweet.replyCount or 0,
        "view_count": tweet.viewCount or 0,
        "quote_count": tweet.quoteCount or 0,
        "links": [link.url for link in tweet.links],
        "photo_urls": [photo.url for photo in tweet.media.photos],
        "hashtags": tweet.hashtags,
    }


async def main():
    parser = argparse.ArgumentParser(description="Fetch latest tweets for one X user via twscrape.")
    parser.add_argument("--username", required=True)
    parser.add_argument("--limit", type=int, default=20)
    parser.add_argument("--accounts-db", default=str(Path(__file__).with_name("accounts.db")))
    args = parser.parse_args()

    api = API(args.accounts_db)
    user = await api.user_by_login(args.username)
    tweets = await gather(api.user_tweets(user.id, limit=args.limit))

    payload = {
        "username": args.username,
        "user_id": str(user.id),
        "tweets": [tweet_to_dict(tweet) for tweet in tweets],
    }

    print(json.dumps(payload, ensure_ascii=False))


if __name__ == "__main__":
    asyncio.run(main())
