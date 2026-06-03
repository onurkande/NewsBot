import g4f
from g4f.client import Client

# 1. ELLE SEÇİM YAPABİLECEĞİN YER (None bırakırsan otomatik seçer)
SECILI_PROVIDER = None 

# Otomatik seçim için sağlayıcı havuzu - burada otomatik model seçimini services\gpt4free\havuz dosyasından çeksin lütfen.
# OTOMATIK_HAVUZ = [
#     "DuckDuckGo", "BlackboxPro", "PollinationsAI", "Liaobots", 
#     "DeepInfra", "Gemini", "HuggingChat", "OperaAria", "You"
# ]

def load_providers_from_havuz():
    """havuz dosyasından sağlayıcıları oku."""
    import os
    havuz_path = os.path.join(os.path.dirname(__file__), 'havuz')
    
    try:
        with open(havuz_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Başlık satırını atla ve sağlayıcıları çıkar
        lines = content.strip().split('\n')[1:]
        providers = [line.strip() for line in lines if line.strip()]
        return providers
    except FileNotFoundError:
        print(f"[!] Hata: {havuz_path} dosyası bulunamadı.")
        return []

OTOMATIK_HAVUZ = load_providers_from_havuz()

def get_provider_by_name(name):
    """İsmi verilen sağlayıcıyı g4f kütüphanesinden çeker."""
    return getattr(g4f.Provider, name, None)

def main():
    client = Client()
    
    # Kullanılacak sağlayıcıyı belirle
    hedef_providerlar = []
    if SECILI_PROVIDER:
        p = get_provider_by_name(SECILI_PROVIDER)
        if p:
            hedef_providerlar = [p]
            print(f"-> Manuel sağlayıcı seçildi: {SECILI_PROVIDER}")
        else:
            print(f"-> Hata: '{SECILI_PROVIDER}' bulunamadı. Otomatik havuza geçiliyor.")
            hedef_providerlar = [get_provider_by_name(n) for n in OTOMATIK_HAVUZ if get_provider_by_name(n)]
    else:
        print("-> Sağlayıcı belirtilmedi, otomatik havuzdan deneniyor...")
        hedef_providerlar = [get_provider_by_name(n) for n in OTOMATIK_HAVUZ if get_provider_by_name(n)]

    # Sorguyu gerçekleştir
    basarili = False
    for p in hedef_providerlar:
        try:
            response = client.chat.completions.create(
                model="", # Otomatik model seçimi
                provider=p,
                messages=[{"role": "user", "content": "Yapay zeka nedir, çok kısaca anlat."}]
            )
            
            # Başarılı ise bilgileri yazdır
            print("\n" + "="*30)
            print(f"SAĞLAYICI: {p.__name__}")
            print(f"MODEL    : {response.model or 'Varsayılan'}")
            print("="*30)
            print("CEVAP:")
            print(response.choices[0].message.content)
            
            basarili = True
            break # Cevabı alınca döngüden çık
            
        except Exception:
            continue # Çalışmazsa hata verme, sıradakine geç

    if not basarili:
        print("\n[!] Hata: Havuzdaki hiçbir sağlayıcıdan yanıt alınamadı.")

if __name__ == "__main__":
    main()