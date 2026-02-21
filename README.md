## Sorgu Sayıları

### Identity
| Endpoint       | Sql İşlem Sayısı |
|:---------------|:-----------------|
| Register       | 4                |
| Login          | 2                |
| ForgotPassword | 5                |
| ResetPassword  | 5                |

### Auth Requests

Her istekte auth requestleri: +3

| Endpoint               | Sql İşlem Sayısı |
|:-----------------------|:-----------------|
| user.index             | 3                |
| user.updateName        | 5                |
| bookshelves.index      | 5                |
| bookshelves.addBook    | 7                |
| bookshelves.updateBook | 8                |
| bookshelves.deleteBook | 8                |
