import argparse
import json
import os
import sys
from pathlib import Path

# Windows asyncio/env fix (sunucu ve local uyumluluk)
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


async def main():
    parser = argparse.ArgumentParser(description="Fetch X user profile via twscrape.")
    parser.add_argument("--username", required=True)
    parser.add_argument("--accounts-db", default=str(Path(__file__).with_name("accounts.db")))
    args = parser.parse_args()

    api = API(args.accounts_db)

    try:
        user = await api.user_by_login(args.username)

        result = {
            "success": True,
            "profile": {
                "display_name": getattr(user, 'display_name', None) or getattr(user, 'displayname', None) or '',
                "followers_count": getattr(user, 'followers_count', 0) or getattr(user, 'followersCount', 0) or 0,
                "following_count": getattr(user, 'friends_count', 0) or getattr(user, 'friendsCount', 0) or getattr(user, 'following_count', 0) or getattr(user, 'followingCount', 0) or 0,
                "statuses_count": getattr(user, 'statuses_count', 0) or getattr(user, 'statusesCount', 0) or 0,
                "profile_image_url": getattr(user, 'profile_image_url_https', None) or getattr(user, 'profileImageUrlHttps', None) or '',
            },
        }
    except Exception as e:
        result = {"success": False, "error": str(e)}

    print(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    asyncio.run(main())
