<?php
namespace models;

require_once '../lib/db.class.php';
require_once '../config/db.php';

class Products {
    public function get(
        array $tags, 
        float $price_from, 
        float $price_to
    ) {
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
SELECT 
    P.*, 
    GROUP_CONCAT(T.name) AS tags,
    M.name AS marketplace_name, 
    M.url AS marketplace_url, 
    M.img AS marketplace_img
FROM products P
$sqlTags
INNER JOIN marketplace M ON (P.marketplace_id = M.id)
INNER JOIN products_tags PTT ON (PTT.products_id = P.id)
INNER JOIN tags T ON (PTT.tags_id = T.id)
WHERE 
	M.record_id IN ( 
		SELECT (SELECT MAX(RR.id) AS max_id FROM records RR WHERE RR.name = R.name) FROM records R GROUP BY name
    )
	AND P.price BETWEEN %d AND %d
GROUP BY P.id
SQL;

        return \DB::query($sql, $price_from, $price_to);
    }
}