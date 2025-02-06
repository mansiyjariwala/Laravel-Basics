<?php

namespace App\Livewire;

use App\Models\College;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
// use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use Illuminate\Support\Facades\Storage;

final class CollegeTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()
                ->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function header(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Bulk Delete')
                ->class('pg-btn-white bg-red-500 text-black dark:border-red-600 dark:hover:bg-red-700 dark:ring-offset-red-800')
                ->dispatch('bulkDelete', []),
        ];
    }

    public function datasource(): Builder
    {
        return College::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('location')
            ->add('image', function($model) {
                if (!$model->image || str_contains($model->image, 'Temp')) {
                    return '<div class="w-8 h-8 rounded-full bg-gray-200"></div>';
                }
                return '<img class="w-8 h-8 shrink-0 grow-0 rounded-full" src="' . asset("storage/{$model->image}") . '">';
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),


            Column::make('Location', 'location')
                ->sortable()
                ->searchable(),

            Column::make('Image', 'image'),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit-college')]
    public function editCollege($rowId): void
    {
        $college = College::find($rowId);

        $this->dispatch('edit-college-form', $college->toArray());
    }

    public function actions(College $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit-college', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('Delete')
                ->id()
                ->class('pg-btn-white bg-red-500 text-black dark:border-red-600 dark:hover:bg-red-700 dark:ring-offset-red-800')
                ->dispatch('delete-college', ['rowId' => $row->id])
        ];
    }

    #[\Livewire\Attributes\On('delete-college')]
    public function deleteCollege($rowId): void
    {
        $this->dispatch('delete-college-form', ['rowId' => $rowId]);
    }

    #[\Livewire\Attributes\On('bulkDelete')]
    public function bulkDelete(): void
    {
        if (empty($this->checkboxValues)) {
            return;
        }

        try {
            $colleges = College::whereIn('id', $this->checkboxValues)->get();

            foreach ($colleges as $college) {
                if ($college->image && !str_contains($college->image, 'Temp')) {
                    Storage::disk('public')->delete($college->image);
                }
                $college->delete();
            }

            $this->checkboxValues = [];
            session()->flash('success', 'Selected colleges deleted successfully!');
            $this->dispatch('pg:eventRefresh-default');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete colleges. Please try again.');
        }
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
