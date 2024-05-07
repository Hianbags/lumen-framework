<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
        protected $table = 'categories';
        protected $fillable = [
            'title','category_id'
        ];
        public $timestamps = false;
        public function children()
        {
            return $this->hasMany(Category::class, 'category_id');
        }
        public function parent()
        {
            return $this->belongsTo(Category::class, 'category_id');
        }
        public function getParentName(){
            return optional($this->parent)->title;
        }
        
}