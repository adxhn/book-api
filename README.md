## Sorgu Sayıları

### Public Requests
| Endpoint       | Sql İşlem Sayısı |
|:---------------|:-----------------|
| Register       | 4                |
| Login          | 2                |
| ForgotPassword | 4                |

### Auth Requests

Her istekte auth requestleri: +3

| Endpoint               | Sql İşlem Sayısı |
|:-----------------------|:-----------------|
| user.index             | 0                |
| user.updateName        | 2                |
| bookshelves.index      | 2                |
| bookshelves.addBook    | 4                |
| bookshelves.updateBook | 3                |
| bookshelves.deleteBook | 3                |
