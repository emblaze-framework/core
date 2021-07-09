<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Validation\Rule;

use Rakit\Validation\Rule;
use Emblaze\Database\Database;

class UniqueRule extends Rule
{
    protected $message = ":attribute :value has been used";

    protected $fillableParams = ['table', 'column', 'except'];


    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');

        if ($except AND $except == $value) {
            return true;
        }

        // do query
        // $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        // $stmt->bindParam(':value', $value);
        // $stmt->execute();
        // $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $data = Database::table($table)->where($column,'=',$value)->first();

        // true for valid, false for invalid
        // return intval($data['count']) === 0;
        return $data ? false : true;
    }
}