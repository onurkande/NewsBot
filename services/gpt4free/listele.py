import g4f.Provider

# Tüm sağlayıcıları bir listede topla
butun_saglayicilar = [p for p in dir(g4f.Provider) if not p.startswith("_")]

print("--- SİSTEMDE YÜKLÜ SAĞLAYICILAR LİSTESİ ---")
for p in butun_saglayicilar:
    print(p)
print("\nBu listeyi kopyalayıp bana yapıştırabilirsin.")