<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Products Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match text used 
    | on the products page on Insura.
    |
    */

    'button'       => [
        'delete' => 'Delete',
        'edit'   => 'Edit',
        'new'    => 'New Product'
    ],
    'input'         => [
        'label'         => [
            'branch_name'           => 'Branch Name',
            'branch_location'       => 'Branch Location',
            'branch_email'          => 'Branch Email',
            'branch_phone'          => 'Branch phone'
        ],
        'option'        => [
            'category'      => 'Add categories in the settings page',
            'sub_category'  => 'Add sub-categories in the settings page'
        ],
        'placeholder'   => [
           'branch_name'           => 'Branch Name',
            'branch_location'       => 'Branch Location',
            'branch_email'          => 'Branch Email',
            'branch_phone'          => 'Branch phone'
        ]
    ],
    'message'       => [
        'error'     => [
            'missing'   => 'No such product exists in your company!'
        ],
        'info'      => [
            'deleted'   => 'Branch deleted!'
        ],
        'success'   => [
            'added'     => 'Branch added!',
            'edited'    => 'Branch edited!'
        ]
    ],
    'modal'         => [
        'button'        => [
            'confirm'   => [
                'edit'  => 'Save',
                'new'   => 'Create'
            ],
            'or'        => 'OR',
            'cancel'    => [
                'edit'  => 'Cancel',
                'new'   => 'Cancel'
            ]
        ],
        'header'        => [
            'edit'  => 'Edit Branch',
            'new'   => 'New Product'
        ],
        'instruction'   => [
            'edit'  => 'Edit Branch',
            'new'   => 'Create a new product'
        ]
    ],
    'swal'  => [
        'warning'   => [
            'delete'    => [
                'confirm'   => 'Yes, delete it!',
                'title'     => 'Are you sure',
                'text'      => 'The product and all related data will be permanently deleted.'
            ]
        ]
    ],
    'table'         => [
        'header'    => [
            'actions'       => 'Actions',
            'category'      => 'Category',
            'insurer'       => 'Insurer',
            'name'          => 'Name',
            'policies'      => 'Policies',
            'sub_category'  => 'Sub Category'
        ],
        'data'      => [
            'not_available' => 'No products have been added yet.',
            'pagination'    => 'Showing :start to :stop of :total'
        ]
    ],
    'title'         => 'Products',

];
