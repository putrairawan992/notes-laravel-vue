# Challenge 4 — PHP Bug Hunt: Answer

## What is wrong with the function?

The bug is a **logical operator error** in the `array_filter` callback.

### The Buggy Code

```php
function getActiveUserNotes(array $notes, int $userId): array
{
    return array_filter($notes, function ($note) use ($userId) {
        return $note['user_id'] === $userId || !$note['is_deleted'];
    });
}
```

### Why it Produces the Wrong Output

The condition uses `||` (logical **OR**) when it should use `&&` (logical **AND**).

The intended logic is:
> Return notes that belong to the given user **AND** have not been deleted.

But the `||` operator means:
> Return notes where **EITHER** the note belongs to the user **OR** the note is not deleted.

This causes note `id: 2` to be incorrectly included because:
- `$note['user_id'] === $userId` → `2 === 1` → **false**
- `!$note['is_deleted']` → `!false` → **true**
- `false || true` → **true** ← The note passes the filter even though it belongs to user 2!

Similarly, if there were a deleted note belonging to user 1 (like note `id: 3`), it would also be incorrectly included:
- `$note['user_id'] === $userId` → `1 === 1` → **true**
- `!$note['is_deleted']` → `!true` → **false**
- `true || false` → **true** ← A deleted note passes because it belongs to the user!

Both conditions must be satisfied simultaneously — the note must be the user's **AND** it must not be deleted.

---

## The Corrected Function

```php
function getActiveUserNotes(array $notes, int $userId): array
{
    return array_filter($notes, function ($note) use ($userId) {
        return $note['user_id'] === $userId && !$note['is_deleted'];
    });
}
```

### Walkthrough with the Given Input

| ID | user_id | is_deleted | Belongs to user 1? | Not deleted? | Both true? | Included? |
|----|---------|------------|---------------------|--------------|------------|-----------|
| 1  | 1       | false      | ✅ true             | ✅ true      | ✅ true    | ✅ Yes    |
| 2  | 2       | false      | ❌ false            | ✅ true      | ❌ false   | ❌ No     |
| 3  | 1       | true       | ✅ true             | ❌ false     | ❌ false   | ❌ No     |

The corrected function now returns exactly the expected output:

```php
[
    ['id' => 1, 'user_id' => 1, 'is_deleted' => false, 'title' => 'My Note'],
]
```
