import sys, types

# Preemptive fix: g4f eagerly imports all providers, including DeepSeekAPI which
# requires the 'wasmtime' native module. On some Windows architectures this import
# can crash with "unsupported architecture". We stub it out before importing g4f.
if 'wasmtime' not in sys.modules:
    _wasmtime_stub = types.ModuleType('wasmtime')
    _wasmtime_stub._ffi = types.ModuleType('wasmtime._ffi')
    sys.modules['wasmtime'] = _wasmtime_stub
    sys.modules['wasmtime._ffi'] = _wasmtime_stub._ffi

import g4f.Provider
from g4f.Provider.base_provider import BaseProvider

providers = []
for name in dir(g4f.Provider):
    if name.startswith('_'):
        continue
    obj = getattr(g4f.Provider, name)
    if isinstance(obj, type) and issubclass(obj, BaseProvider) and obj is not BaseProvider:
        providers.append(name)

print("--- SİSTEMDE YÜKLÜ SAĞLAYICILAR LİSTESİ ---")
for p in providers:
    print(p)
print(f"\nToplam {len(providers)} sağlayıcı bulundu.")
print("\nBu listeyi kopyalayıp bana yapıştırabilirsin.")
