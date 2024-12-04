<?php


use App\Models\AssignedRequest;
use App\Models\TechnicalStaff;
use Illuminate\Support\Facades\Cache;

use function Livewire\Volt\{computed, mount, on, state};

//
state(['id', 'AssignedRequest']);

on(['techs' => function(){
    $this->AssignedRequest = AssignedRequest::where('request_id', $this->id)->get();
}]);

mount(function () {
    $this->id = session('requestId');
    $this->AssignedRequest = AssignedRequest::where('request_id', $this->id)->get();
});


$viewTechStaff = computed(function ($arr = null) {
    if (is_null($arr)) {
        return Cache::flexible('tech',[5,10], function(){
            return TechnicalStaff::with(['user', 'AssignedRequest'])->get();
        });
    } else {
        return TechnicalStaff::whereIn('technicalStaff_id', $arr)->with(['user', 'AssignedRequest'])->get();
    }
});

$assignTask = function ($techId) {

    $AssignedRequest = AssignedRequest::create([
        'technicalStaff_id' => $techId,
        'request_id' => session('requestId')
    ]);
    $AssignedRequest->save();
    $this->dispatch('techs');
    $this->dispatch('success', 'Assigned');
};

$removeTask = function ($techId) {
    $AssignedRequest = AssignedRequest::where('technicalStaff_id', $techId)->where('request_id', $this->id)->with("TechnicalStaff")->first();
    $this->dispatch('techs');
    $AssignedRequest->delete();
};


$viewAssigned = function () {
    
    $tech = $this->AssignedRequest->pluck('technicalStaff_id')->toArray();
    return $tech;
};
?>

<div>

    @include('components.assigned-request.view-assigned-request')

</div>