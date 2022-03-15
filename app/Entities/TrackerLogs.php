<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category.
 *
 * @package namespace App\Entities;
 */
class TrackerLogs extends Model
{
    protected $table = 'tracker_logs';

    public function getTitle(): string
    {
        return 'Brak aktywności w ciągu ' . $this->time. ' minut ';
    }

    public function getContent(): string
    {
        $content = 'Brak aktywności na stronie ' . $this->page . ' ';
        $content .= 'Data ' . $this->created_at . ' ';
        if ($this->description !== '') {
            $content .= 'Powód bezczynności: ' . $this->description . ' ';
        }else{
            $content .= 'Brak powodu bezczynności';
        }

        return $content;
    }
}
