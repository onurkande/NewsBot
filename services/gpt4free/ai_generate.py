#!/usr/bin/env python3
"""
AI Haber Uretim Scripti - gpt4free entegrasyonu

Kullanim:
    python ai_generate.py --prompt "AI prompt metni" [--model "DuckDuckGo"]

Model listesini havuz dosyasindan okur. Belirtilen model calismazsa otomatik diger modellere gecer.
Cikti JSON formatinda stdout'a yazilir.
"""

import argparse
import json
import sys
import os
import traceback

# Fix for running under PHP/web server where HOME/USERPROFILE may not be set.
# g4f's config.py calls Path.home() which needs these.
# Also async I/O on Windows needs SYSTEMROOT/WINDIR env vars.
def _ensure_home_env():
    if sys.platform == "win32":
        # SYSTEMROOT / WINDIR are needed for _overlapped / asyncio on Windows
        if "SYSTEMROOT" not in os.environ:
            os.environ["SYSTEMROOT"] = os.environ.get("WINDIR", r"C:\Windows")
        if "WINDIR" not in os.environ:
            os.environ["WINDIR"] = os.environ["SYSTEMROOT"]

        if "USERPROFILE" not in os.environ:
            userprofile = os.environ.get("HOME") or (
                os.environ.get("HOMEDRIVE", "") + os.environ.get("HOMEPATH", "")
            )
            if userprofile:
                os.environ["USERPROFILE"] = userprofile
            else:
                os.environ["USERPROFILE"] = os.path.dirname(os.path.abspath(__file__))
        if "HOME" not in os.environ:
            os.environ["HOME"] = os.environ["USERPROFILE"]
    else:
        if "HOME" not in os.environ:
            home = os.environ.get("USERPROFILE") or os.environ.get("HOMEDRIVE", "") + os.environ.get("HOMEPATH", "")
            if home:
                os.environ["HOME"] = home
            else:
                os.environ["HOME"] = os.path.dirname(os.path.abspath(__file__))

_ensure_home_env()
if "PATH" not in os.environ:
    os.environ["PATH"] = "/usr/local/bin:/usr/bin:/usr/local/sbin:/usr/sbin:/bin:/sbin"

# Unbuffered stdout so PHP can read JSON output immediately
os.environ.setdefault("PYTHONUNBUFFERED", "1")

try:
    import g4f
    from g4f.client import Client
except ImportError:
    print(json.dumps({"success": False, "error": "g4f kutuphanesi yuklu degil. pip install g4f ile yukleyin."}))
    sys.exit(1)


def load_provider_pool(pool_path=None):
    """
    havuz dosyasindan calisan provider isimlerini okur.
    Dosyada her satirda bir provider ismi bulunur.
    """
    if pool_path is None:
        script_dir = os.path.dirname(os.path.abspath(__file__))
        pool_path = os.path.join(script_dir, "havuz")

    providers = []
    if os.path.exists(pool_path):
        with open(pool_path, "r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                # Yorum satirlarini ve bos satirlari atla
                if line and not line.startswith("---") and not line.startswith("#"):
                    providers.append(line)
    else:
        # Varsayilan kararli havuz
        providers = [
            "DuckDuckGo", "BlackboxPro", "PollinationsAI", "Liaobots",
            "DeepInfra", "Gemini", "HuggingChat", "OperaAria", "You"
        ]

    return providers


def get_provider_by_name(name):
    """Ismi verilen saglayiciyi g4f kutuphanesinden ceker."""
    return getattr(g4f.Provider, name, None)


def generate(prompt_text, preferred_model=None):
    """
    AI'dan yanit uretir. Belirtilen model calismazsa havuzdaki sirayla dener.
    Basarili olursa dict dondurur, basarisiz olursa hata dict'i dondurur.
    """
    client = Client()
    pool = load_provider_pool()

    target_providers = []
    tried_providers = []

    if preferred_model:
        p = get_provider_by_name(preferred_model)
        if p:
            target_providers = [p]
        else:
            # Belirtilen model bulunamadi, havuza don
            target_providers = [get_provider_by_name(n) for n in pool if get_provider_by_name(n)]
    else:
        target_providers = [get_provider_by_name(n) for n in pool if get_provider_by_name(n)]

    if not target_providers:
        return {
            "success": False,
            "error": "Havuzda calisabilir hicbir provider bulunamadi.",
            "tried_providers": [],
        }

    for p in target_providers:
        provider_name = p.__name__ if hasattr(p, '__name__') else str(p)
        tried_providers.append(provider_name)

        try:
            response = client.chat.completions.create(
                model="",
                provider=p,
                messages=[{"role": "user", "content": prompt_text}],
            )

            content = response.choices[0].message.content if response.choices else ""
            model_used = response.model or "varsayilan"

            return {
                "success": True,
                "provider": provider_name,
                "model": model_used,
                "content": content,
            }

        except Exception as e:
            # Bu provider calismadi, siradakine gec
            continue

    return {
        "success": False,
        "error": f"Havuzdaki {len(tried_providers)} provider denendi, hicbirinden yanit alinamadi.",
        "tried_providers": tried_providers,
    }


def main():
    parser = argparse.ArgumentParser(description="AI Haber Uretim Scripti")
    parser.add_argument("--prompt", required=True, help="AI prompt metni")
    parser.add_argument("--model", default=None, help="Kullanilacak model adi (opsiyonel)")

    args = parser.parse_args()

    try:
        result = generate(args.prompt, args.model)
        print(json.dumps(result, ensure_ascii=False))
        sys.exit(0 if result.get("success") else 1)

    except Exception as e:
        error_result = {
            "success": False,
            "error": f"Beklenmeyen hata: {str(e)}",
            "traceback": traceback.format_exc(),
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)


if __name__ == "__main__":
    main()
