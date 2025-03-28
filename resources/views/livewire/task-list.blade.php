<?php

use App\Events\RequestEvent;
use App\Models\Categories;
use App\Models\Category;
use App\Models\Request;
use App\Models\TaskList;
use App\Models\User;
use App\Notifications\RequestStatus;
use Illuminate\Support\Facades\Cache;

use function Livewire\Volt\{state, mount, rules};

state(['checked', 'page',]);


state(['category', 'task', 'request']);

rules([
    'task' => 'required'
]);

mount(function () {


    $this->request = Request::find(session('requestId'));



    /* 
    if (session('page') == 'request') {
        //to get the percentage
        $this->checked = round($this->request->progress / 100 * count($this->request->category->taskList));
    } */
});

$addTaskList = function () {
    $this->validate();

    // Check if the task already exists in the same category
    $existingTask = TaskList::where('category_id', $this->category)
        ->where('task', $this->task)
        ->first();

    if ($existingTask) {
        // Dispatch a danger alert if task already exists
        $this->dispatch('danger', 'Task already exists in this category');

        return; // Exit the function if task exists
    }


    // Create the new task
    $taskList = TaskList::create([
        'category_id' => $this->category,
        'task' => $this->task,
        'status' => 'enabled',
    ]);

    $taskList->save();

    $this->reset();

    $this->category = $taskList->category_id;

    // Dispatch success message
    $this->dispatch('success', 'Task Successfully added');
};


$updateStatus = function ($id) {
    $list = TaskList::find($id);

    if ($list) {
        $list->status = ($list->status === 'enabled') ? 'disabled' : 'enabled';
        $list->save();
    }
};

$viewTaskList = function () {

    return TaskList::where('category_id', $this->category)
        ->get();
};


$updateList = function ($item, $pos) {
    dd([$item, $pos]);
}

?>

<div class="relative rounded-md">


    <ul x-sort="@this.call('updateList', $item, $position)">
        <div class="whitespace-nowrap mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000">
                <path d="M444-288h72v-240h-72v240Zm35.79-312q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm.49 504Q401-96 331-126t-122.5-82.5Q156-261 126-330.96t-30-149.5Q96-560 126-629.5q30-69.5 82.5-122T330.96-834q69.96-30 149.5-30t149.04 30q69.5 30 122 82.5T834-629.28q30 69.73 30 149Q864-401 834-331t-82.5 122.5Q699-156 629.28-126q-69.73 30-149 30Zm-.28-72q130 0 221-91t91-221q0-130-91-221t-221-91q-130 0-221 91t-91 221q0 130 91 221t221 91Zm0-312Z" />
            </svg>
            <p class="text-sm text-black font-medium text-left md:text-center text-wrap">Drag and drop to rearrange the list items.</p>
        </div>

        @foreach($this->viewTaskList() as $list)
        <li class="text-blue-950 text-lg" x-sort:item="{{$list->id}}">
            <div style="border: 1px solid #2e5e91; border-radius: 6px; padding: 8px; margin-bottom: 8px;"
                class=" flex flex-wrap items-center justify-between gap-2  {{$list->status == 'disabled' ? 'bg-slate-300 hover:bg-slate-400' : 'hover:bg-blue-50'}}">

                <span class="whitespace-normal break-words flex-1 font-medium cursor-grab">
                    {{$list->task}}
                </span>

                <button type="button" wire:loading.attr="disabled" @click="$wire.updateStatus({{$list->id}})" class="cursor-pointer p-2 border rounded-md border-blue-600">
                    {{$list->status}}
                </button>


            </div>

        </li>

        @endforeach
    </ul>


    @switch(session('page'))

    @case('category')

    <div class="md:x y gap-2 flex items-center justify-between">
        <div class="flex flex-row items-start gap-2 w-[100%] md:w-full">
            <div class="w-[80%] md:w-full">
                <input type="text" wire:model="task" class="input w-full" style="border: 1px solid #2e5e91;" placeholder="Enter task item...">

                <!-- Show error message if there's an error for the 'task' field -->
                @error('task')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex-none">
                <button class="w-20 text-white font-medium rounded-md px-4 py-2 text-lg" style="background-color: #3E7B27" wire:click.prevent="addTaskList">Add</button>
            </div>
        </div>
    </div>



    @break

    @case('request')
    @if($this->viewTaskList()->isEmpty())
    <div class="flex flex-col">
        <span class="text-red-500 font-semibold" style="font-size: 16px;">No Task List Found.</span>
        <a href="/category" class="text-blue underline" style="font-size: 16px;">Proceed to this link to add task on a category...</a>
    </div>
    @else
    <div class="float-end">
        @include('components.assigned-request.button')
    </div>
    @endif
    @break

    @endswitch


</div>