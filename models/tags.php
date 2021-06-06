<?php
namespace models;

require_once '../lib/db.class.php';
require_once '../config/db.php';

class Tags
{

    public function get(
        string $name,
        int $lang_id,
        int $country_id,
        array $tags
    ): array
    {
        if (!empty($tags)) {
            $sqlTags = [];
            foreach ($tags as $index => $tag) {
                $sqlTags[] = "INNER JOIN products_tags PTT$index ON (PTT$index.products_id = P.id AND PTT$index.tags_id = $tag)";
            }
            $sqlTags = implode("\n", $sqlTags);
            $tagsNot = ' AND NOT (T.id IN (' . implode(',', $tags) . '))';
        } else {
            $sqlTags = '';
            $tagsNot = '';
        }

        $sql = <<<SQL
SELECT T.*, COUNT(distinct(P.id)) AS products_count
FROM tags T
INNER JOIN products_tags PT ON (PT.tags_id = T.id)
INNER JOIN products P ON (PT.products_id = P.id)
$sqlTags
INNER JOIN marketplace M ON (P.marketplace_id = M.id)
WHERE 
	M.record_id IN ( 
		SELECT (SELECT MAX(RR.id) AS max_id FROM records RR WHERE RR.name = R.name) FROM records R GROUP BY name
    )
	AND T.name LIKE %s
    AND T.lang_id = %i
    AND M.country_id = %i
    $tagsNot
GROUP BY T.id
ORDER BY products_count DESC
SQL;
        
        return \DB::query($sql, '%' . $name . '%', $lang_id, $country_id);
    }

    public function getTotalProducts(array $tags)
    {
        if (!empty($tags)) {
            $sqlTags = [];
            foreach ($tags as $index => $tag) {
                $sqlTags[] = "INNER JOIN products_tags PTT$index ON (PTT$index.products_id = P.id AND PTT$index.tags_id = $tag)";
            }

            $sqlTags = implode("\n", $sqlTags);
        } else {
            $sqlTags = '';
        }

        $sql = <<<SQL
SELECT COUNT(DISTINCT(P.id)) AS products_count
FROM products P
$sqlTags
INNER JOIN marketplace M ON (P.marketplace_id = M.id)
WHERE 
	M.record_id IN ( 
		SELECT (SELECT MAX(RR.id) AS max_id FROM records RR WHERE RR.name = R.name) FROM records R GROUP BY name
    )
SQL;
        return \DB::queryFirstField($sql);
    }

    public function find_tag($tag)
    {
        return \DB::queryFirstRow("SELECT * FROM tags T WHERE T.name = %s", $tag);
    }

    public function search_tag($tag)
    {
        return \DB::queryFirstRow("SELECT * FROM tags T WHERE T.name LIKE %s", "%$tag%");
    }

    public function find_tags(string $tags): array
    {
        $tags = explode(',', $tags);
        $result = [];

        if (count($tags) === 1) {
            $result[] = $this->search_tag($tags[0]);
        } elseif (count($tags) > 1) {
            $lastTag = array_pop($tags);

            foreach ($tags as $tag) {
                $res = $this->find_tag($tag);

                if (!empty($res)) {
                    $result[] = $res;
                }
            }

            $res = $this->search_tag($lastTag);

            if (!empty($res)) {
                $result[] = $res;
            }

        } else {
            return [];
        }

        return $result;
    }

    public function find_tags_id(string $tags)
    {
        $tags = $this->find_tags($tags);

        return array_map(function ($row) {
            return $row['id'];
        }, $tags);
    }

    public function getNamesFromId(array $id_list): array
    {
        $sqlTagsIn = implode(',', $id_list);

        if (empty($sqlTagsIn)) {
            $sqlTagsIn = '0';
        }

        $sql = <<<SQL
            SELECT T.*
            FROM tags T
            WHERE T.id IN ($sqlTagsIn)
SQL;
        $result = \DB::query($sql);

        $result = array_map(function($row) {
            $row['products_count'] = $this->getTotalProducts([$row['id']]);
            return $row;
        }, $result);

        return $result;
    }

    public function saveParams(array $params)
    {
        $_SESSION['paramTags'] = $params;
    }

    public function restoreParams(): array
    {
        return $_SESSION['paramTags'];
    }

    public function saveTags(array $params)
    {
        $_SESSION['savedTags'] = $params;
    }

    public function restoreTags(): array
    {
        if (isset($_SESSION['savedTags'])) {
            return $_SESSION['savedTags'];
        } else {
            return [];
        }
        
    }
}
