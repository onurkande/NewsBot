from session import account

tweet = account.tweet(
    text="""Sessizliğin içinde bile gücünü hissettiren bir canlı varsa o da kaplandır. 🐅
Doğanın en karizmatik, en dikkat çekici ve en etkileyici avcılarından biri.
Bazı hayvanlar sadece görünür, bazıları ise iz bırakır.""",
    media=[{"media": "kaplan.jpeg"}]
)

print("Tweet gönderildi!")