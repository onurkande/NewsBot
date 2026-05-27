import asyncio
import json
from pathlib import Path
from urllib.parse import urlparse
from urllib.request import urlretrieve

from twscrape import API, gather


def download_file(url: str, dst_dir: Path, prefix: str) -> str:
    dst_dir.mkdir(parents=True, exist_ok=True)

    parsed = urlparse(url)
    suffix = Path(parsed.path).suffix or ".jpg"
    dst_path = dst_dir / f"{prefix}{suffix}"

    urlretrieve(url, dst_path)
    return str(dst_path)


async def main():
    api = API()  # default accounts.db kullanır

    user_login = "bpthaber"
    user = await api.user_by_login(user_login)

    tweets = await gather(api.user_tweets(user.id, limit=5))

    output = {
        "username": user_login,
        "user_id": user.id,
        "tweets": []
    }

    media_root = Path("media") / user_login

    for tweet in tweets:
        photo_urls = [photo.url for photo in tweet.media.photos]
        downloaded_files = []

        if photo_urls:
            tweet_media_dir = media_root / str(tweet.id)
            for idx, url in enumerate(photo_urls, start=1):
                saved_path = download_file(url, tweet_media_dir, f"photo_{idx}")
                downloaded_files.append(saved_path)

        output["tweets"].append({
            "tweet_id": tweet.id,
            "tweet_url": tweet.url,
            "text": tweet.rawContent,
            "photo_urls": photo_urls,
            "downloaded_files": downloaded_files,
        })

    with open(f"{user_login}_last5.json", "w", encoding="utf-8") as f:
        json.dump(output, f, ensure_ascii=False, indent=2)

    print(f"JSON kaydedildi: {user_login}_last5.json")
    print("İşlem tamamlandı.")


if __name__ == "__main__":
    asyncio.run(main())