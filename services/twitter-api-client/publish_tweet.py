#!/usr/bin/env python3
"""
Tweet Publish Script - twitter-api-client entegrasyonu

Kullanim:
    python publish_tweet.py --payload-file /path/to/payload.json

Payload formati:
    {
        "text": "Tweet metni",
        "media_paths": ["storage/app/media/tweets/123/a.jpg"],
        "cookies": {
            "auth_token": "...",
            "ct0": "..."
        }
    }

Cikti JSON formatinda stdout'a yazilir.
"""

import argparse
import json
import sys
import os
import traceback

# Fix for Windows env
if sys.platform == "win32":
    if "SYSTEMROOT" not in os.environ:
        os.environ["SYSTEMROOT"] = os.environ.get("WINDIR", r"C:\Windows")
    if "WINDIR" not in os.environ:
        os.environ["WINDIR"] = os.environ["SYSTEMROOT"]

os.environ.setdefault("PYTHONUNBUFFERED", "1")
os.environ.setdefault("PYTHONIOENCODING", "utf-8")

try:
    from twitter.account import Account
except ImportError:
    print(json.dumps({"success": False, "error": "twitter-api-client kutuphanesi yuklu degil."}))
    sys.exit(1)


def load_payload(path):
    with open(path, "r", encoding="utf-8") as f:
        return json.load(f)


def resolve_media_paths(media_paths):
    """Relative storage path'leri absolute path'e cevirir."""
    resolved = []
    for p in media_paths:
        if os.path.isabs(p):
            resolved.append(p)
        else:
            # Laravel storage/app/... goreceli yollari proje root ile birlestir
            base = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
            full = os.path.join(base, p)
            if os.path.exists(full):
                resolved.append(full)
            else:
                # Storage root altinda aramaya devam et
                alt = os.path.join(base, "storage", "app", p)
                if os.path.exists(alt):
                    resolved.append(alt)
                else:
                    resolved.append(full)
    return resolved


def publish(payload):
    text = payload.get("text", "")
    media_paths = payload.get("media_paths", [])
    cookies = payload.get("cookies", {})

    auth_token = cookies.get("auth_token")
    ct0 = cookies.get("ct0")

    if not auth_token or not ct0:
        return {
            "success": False,
            "error": "auth_token ve ct0 gerekli.",
        }

    account = Account(cookies={"auth_token": auth_token, "ct0": ct0})

    resolved = resolve_media_paths(media_paths)
    media = []
    for path in resolved:
        if os.path.exists(path):
            media.append({"media": path})

    try:
        if media:
            tweet = account.tweet(text=text, media=media)
        else:
            tweet = account.tweet(text=text)

        data = tweet.get("data", {}) if isinstance(tweet, dict) else {}
        tweet_id = data.get("id", "")

        return {
            "success": True,
            "tweet_id": str(tweet_id),
            "raw": tweet,
        }
    except Exception as e:
        return {
            "success": False,
            "error": str(e),
            "traceback": traceback.format_exc(),
        }


def main():
    parser = argparse.ArgumentParser(description="Tweet Publish Script")
    parser.add_argument("--payload-file", required=True, help="JSON payload dosyasi")
    args = parser.parse_args()

    try:
        payload = load_payload(args.payload_file)
    except Exception as e:
        print(json.dumps({"success": False, "error": f"Payload okunamadi: {str(e)}"}, ensure_ascii=False))
        sys.exit(1)

    result = publish(payload)
    print(json.dumps(result, ensure_ascii=False))
    sys.exit(0 if result.get("success") else 1)


if __name__ == "__main__":
    main()
