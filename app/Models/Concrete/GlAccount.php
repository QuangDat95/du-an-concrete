<?php

namespace App\Models\Concrete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class GlAccount extends Model
{
    use HasFactory,SoftDeletes;
    use NodeTrait {
        NodeTrait::replicate as replicateNode;
    }
    protected $connection = 'mysql';
    public function replicate(array $except = null)
    {
        $instance = $this->replicateNode($except);
        (new SlugService())->slug($instance, true);

        return $instance;
    }

    protected $fillable = ['account_code','name','description','nature_id','customer_flag','created_by','level','parent_id'];
    
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function getLftName()  
    {
        return '_lft';
    }

    public function getRgtName()
    {
        return '_rgt';
    }

    public function getParentIdName()
    {
        return 'parent_id';
    }

    public function setParentAttribute($value)
    {
        $this->setParentIdAttribute($value);
    }
}