import g4f
from g4f.client import Client

# 1. ELLE SEÇİM YAPABİLECEĞİN YER (None bırakırsan otomatik seçer)
SECILI_PROVIDER = None 

# Otomatik seçim için kararlı sağlayıcı havuzu
OTOMATIK_HAVUZ = [
    "DuckDuckGo", "BlackboxPro", "PollinationsAI", "Liaobots", 
    "DeepInfra", "Gemini", "HuggingChat", "OperaAria", "You"
]

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
                messages=[{"role": "user", "content": "Yapay zeka nedir, kısaca anlat."}]
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