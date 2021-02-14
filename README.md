# Livewire Screencasts

#Command Line tips
- List all livewire words
 php artisan | grep livewire
- Move the file using terminal
php artisan livewire:mv register auth.register

Route::livewire() - disabled in 2.0 livewire
Route::get('/', Component::class) -> now

#Gotcha's - wire:click on button vs wire:submit.prevent for form submission

-- We don't use @click on button to submit the form
because in that case we won't be able to submit the form by enter

-- we do .prevent on submit because we don't want the form to be submited and 
see all the values in the url like hitting a get request.

-- the data in Livewire doesn't live in the request
but on the public properties

# Tests
->set to set the property values
->call to call methods

- One test do one thing! Don't fill it with a bunch of assertions

#### Validation Tests
- assertHasErrors better than assertSee and you can specify the
validation rule.

#### Registration Tests
- assertRedirect
- assertTrue - some of the fields ->exists()

#### Tip of the day:
- Always start with test checking if the page has a livewire
component, because if you broke the code the tests will still pass

# Dispatching Browser Events
$this->dispatchBrowserEvent('eventName', 'message');

You can add this event to any dom element the $0.addEventListener('notify',console.log)
$0.addEventListener('notify', (e) => {
alert(e.detail);
});
the next argument of this is added on detail 

# Defer Loading
wire:init - run an action as soon as the component is rendered. 
This can be helpful in cases where you don't want to hold up the entire page load, but want to load some data immediately after the page load.

# This is AlpineJS thing. - Distinguish them!
x-init - its called whenever the component is added on the page (rendered).
x-init runs only on first time
x-ref="this", you can reference the current dom element
then to call it you can do
$refs.this.remove() or other action

# Emit Events
If you want to emit an event:
$this->emit('notify-saved');

To listen: you can add $listeners =[];
but
another way
This is global 
in x-init = "window.livewire.on('notify-saved', () => {});

But we don't want to be global, but just on the click, because:
- if we have two of these components on a page
the livewire event will fire and both of them will pick it up
because this is listening globally

So we use:
$this->emitSelf('notify-saved');

and then to listen there is a helper
x-init="
@this.on('notify-saved', () => {
    setTimeout(() => { 
    open =false,
    }, 3500);
    open = true;
});
"

This is an alias for:
window.livewire.find('someid').on ..
every livewire component has its own id!
you can do:
@this.set()
@this.cal('method');

You can use:
x-show.transition.out.duration.1000ms
or in

# Blade Components

- you can add props to it after the declaration:
<x-input.text leading-add-on/>
@props([
'leadingAddOn' => false,
'You Define Props Here'
])
You can use @isset if its not specified, to avoid error

# Don't change the div after dom refreshing
wire:ignore
Updating other inputs livewire loops through the dom
and refreshes the dom. 
But if we add something with plain js to it, like count and we make a change
on the livewire input, the change made with js won't appear
That's why we use wire:ignore on a div. The disadvantage is that
we cannot use wire:model on that element. This can all can be
solved easily with alpine.

wire:model
On keypress it fires an "input" event and wire:model listens to that
event get its value and send it to livewire.
So to implement wire:model on button click with alpine
we need to add @click="count++; $dispatch('input', count);
$dispatch - magic alpine method

wire:model.lazy
This one listens for change event 

if we put wire:model on a div and inside that div
we add input element, the wire:model will still listen
to the event

When you apply wire:model on div it doesn't debounce automatically like
in the input elements and it will send ajax requests on every letter
you type in. So you must specify wire:model.debounce

# Reference
$wire.user.birthday
$wire gets the value from the field

@entangle = $wire.entangle
x-data="{ value: @entangle('user.birthday') }"
instead of passing user.birthday we can use $attributes->wire('model')

If we use @entangle we don't need to use wire:model
on the input. But we want to pass some other attributes like class or id.
Then we do this:
{{ $attributes->whereDoesntStartWith('wire:model');

then we don't need to dispatch the event, and we assign the value to the
event.target.value

alpine thing $watch
Wathches for value change


You can only wire model bind to the modals directly 
you must have a $rules array. 
For safety reasons. All the fields are not exposed to the frontend
you only reach for those you want to modify.

Computed Property

if we use function that returns query like transactions
it will fire db query everytime is called. 
Computed properties are smart and will be accessed in one
livecycle call and they will be cached.

Query builder vs Eloquent

When you are adding a query on a model like
->whereSomething, also you can keep chaining, then when you do
->get() the query is executed and you have a 
eloquent collection.

But some things needs to be executed on a query
not on the collection like:
Transaction::query()->delete.
