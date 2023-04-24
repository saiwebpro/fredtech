<?php

namespace spark\models;

/**
* PostModel
*
*/
class PostModel extends Model
{
    protected static $table = 'posts';

    protected $queryKey = 'post_id';

    protected $autoTimestamp = true;

    const TYPE_ORIGINAL = 'original_post';
    const TYPE_IMPORTED = 'imported_post';

    protected $sortRules = [
        'newest'            => ['created_at' => 'DESC'],
        'oldest'            => ['created_at' => 'ASC'],
        'post-publish-date' => ['post_pubdate' => 'DESC'],
        'most-popular'      => ['post_hits' => 'DESC'],
        'a2z'               => ['post_title'  => 'ASC'],
        'z2a'               => ['post_title'  => 'DESC'],
    ];

    /**
     * Deletes old posts older than a specific timestamp
     *
     * @param  integer $olderThan The timestamp
     * @param  array  $type The post types to delete, if empty everything will be deleted
     * @return boolean
     */
    public function purgeOldPosts($olderThan, array $type = [])
    {
        $db = $this->db();

        $q = $db->delete()
                ->from($this->getTable())
                // Posts that were created before the timestamp
                ->where('created_at', '<=', $olderThan);

        foreach ($type as $postType) {
            $q = $q->where('post_type', '=', $postType);
        }


        return $q->execute();
    }
}
