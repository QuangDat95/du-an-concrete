<?php

namespace App\Models\Concrete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concrete\OrganizationType;
use App\Models\Concrete\Area;
use Kalnoy\Nestedset\NodeTrait;

class Organization extends Model
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

    protected $fillable = ['name','address','tax_number','email','organization_type_id','area_id','parent_id'];
    
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

    public function organizationtype()
    {
        return $this->belongsTo(OrganizationType::class,'organization_type_id','id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class,'area_id','id');
    }

    public function parent()
    {
        $ancestors = $this->where('id', '=', $this->parent_id)->get();
    while ($ancestors->last() && $ancestors->last()->parent_id !== null)
    {
        $parent = $this->where('id', '=', $ancestors->last()->parent_id)->get();
        $ancestors = $ancestors->merge($parent);
    }
 
    return $ancestors;
    }
}