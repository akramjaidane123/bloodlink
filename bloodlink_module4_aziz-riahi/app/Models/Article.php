<?php
/** Article 1 --- N Commentaire ; Article N --- 1 Catégorie */
class Article extends Model
{
    public static function latest(int $limit = 20): array
    {
        return self::all(
            'SELECT a.id, a.title, a.created_at, SUBSTRING(a.content, 1, 160) AS excerpt, c.name AS category
             FROM articles a JOIN categories c ON c.id = a.category_id ORDER BY a.created_at DESC, a.id DESC LIMIT ' . (int) $limit
        );
    }

    public static function find(int $id): ?array
    {
        return self::one(
            'SELECT a.*, c.name AS category, u.username AS author FROM articles a
             JOIN categories c ON c.id = a.category_id LEFT JOIN users u ON u.id = a.author_id WHERE a.id = :id',
            ['id' => $id]
        );
    }

    public static function comments(int $articleId): array
    {
        return self::all(
            "SELECT m.content, m.created_at, u.username FROM comments m JOIN users u ON u.id = m.user_id
             WHERE m.article_id = :a AND m.status = 'visible' ORDER BY m.created_at DESC",
            ['a' => $articleId]
        );
    }

    public static function addComment(int $articleId, int $userId, string $content): void
    {
        self::run('INSERT INTO comments (article_id, user_id, content) VALUES (:a, :u, :c)',
            ['a' => $articleId, 'u' => $userId, 'c' => $content]);
    }
}
