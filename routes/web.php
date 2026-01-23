<?php 

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\LoginController; // Import the LoginController
use App\Http\Controllers\Auth\RegisterController; // Import the RegisterController
use App\Http\Controllers\Auth\ForgotPasswordController; // Import the ForgotPasswordController
use App\Http\Controllers\Auth\ResetPasswordController; // Import the ResetPasswordController
use App\Http\Controllers\IndexController; // Import the ResetPasswordController
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MproductController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MutualfundController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\InboxController;

// Reminder routes...
Route::post('reminder/{company}', [ReminderController::class, 'update'])->name('reminder.update');
Route::delete('reminder/{reminder}', [ReminderController::class, 'delete'])->name('reminder.delete');

Route::get('/', [IndexController::class, 'get'])->name('home');

// Communication routes...
Route::delete('emails/{email}', [EmailController::class, 'delete'])->name('emails.delete');
Route::get('communication/emails/{recipient}', [EmailController::class, 'getAll'])->name('emails.all');
Route::post('emails', [EmailController::class, 'add'])->name('emails.add');

Route::delete('texts/{text}', [TextController::class, 'delete'])->name('texts.delete');
Route::get('communication/texts/{recipient}', [TextController::class, 'getAll'])->name('texts.all');
Route::post('texts', [TextController::class, 'add'])->name('texts.add');

// Setup & installation routes...
Route::get('setup/cache', [SetupController::class, 'cache']);
Route::get('setup/install', [SetupController::class, 'install']);
Route::get('setup', [SetupController::class, 'get']);
Route::post('setup/configure', [SetupController::class, 'configure']);

// Dynamic JS asset route
Route::get('assets/js/insura.js', [IndexController::class, 'javascript']);

// Dashboard route
Route::get('dashboard', [IndexController::class, 'getDashboard'])->name('dashboard');

// Authentication routes (custom authentication)
Route::get('auth/logout', [AuthController::class, 'getLogout'])->name('auth.logout');
Route::get('logout', [AuthController::class, 'getLogout'])->name('logout'); // Alias for logout
Route::get('auth', function () {
    return view('global.auth');
})->name('auth.show');
Route::post('auth/login', [AuthController::class, 'postLogin'])->name('auth.login');
Route::post('login', [AuthController::class, 'postLogin'])->name('login'); // Alias for login
Route::post('auth/register', [AuthController::class, 'postRegister'])->name('auth.register');
Route::post('register', [AuthController::class, 'postRegister'])->name('register'); // Alias for register

// Activation routes...
Route::get('auth/activate/{token}', [PasswordController::class, 'getActivate'])->name('password.activate.get');
Route::post('auth/activate', [PasswordController::class, 'postActivate'])->name('password.activate');

// Password reset routes...
Route::get('auth/reset/{token}', [PasswordController::class, 'getReset'])->name('password.reset.get');
Route::post('auth/reset/email', [PasswordController::class, 'postEmail'])->name('password.reset.email');
Route::post('password/email', [PasswordController::class, 'postEmail'])->name('password.email'); // Alias
Route::post('auth/reset/password', [PasswordController::class, 'postReset'])->name('password.reset.password');
Route::post('password/reset', [PasswordController::class, 'postReset'])->name('password.update'); // Alias
Route::post('auth/reset', [PasswordController::class, 'update'])->name('password.reset.update');

// Password update route...
Route::post('password/update', [PasswordController::class, 'update'])->name('password.change');

// User routes...


// User routes...
Route::post('users/{user}', [UserController::class, 'edit'])->name('user.edit');

// Settings routes...
Route::get('settings/cache', [SettingController::class, 'cache'])->name('settings.cache');
Route::get('settings/load', [SettingController::class, 'load'])->name('settings.load');
Route::get('settings', [SettingController::class, 'get'])->name('settings.index');
Route::post('settings', [SettingController::class, 'edit'])->name('settings.update');

// Company routes...
Route::delete('companies/{company}', [CompanyController::class, 'delete'])->name('companies.delete');
Route::get('companies', [CompanyController::class, 'getAll'])->name('companies.all');
Route::post('companies', [CompanyController::class, 'add'])->name('companies.add');
Route::post('companies/{company}', [CompanyController::class, 'edit'])->name('companies.edit');
Route::post('editcompanies', [CompanyController::class, 'editcompanies'])->name('companies.editall');

// Product routes...
Route::get('products', [ProductController::class, 'getAll'])->name('products.all');
Route::get('products/{product}', [ProductController::class, 'getOne'])->name('products.one');
Route::post('products', [ProductController::class, 'add'])->name('products.add');
Route::post('products/{product}', [ProductController::class, 'edit'])->name('products.edit');
Route::delete('products/{product}', [ProductController::class, 'delete'])->name('products.delete');

// MProduct routes...
Route::get('mproducts', [MproductController::class, 'getAll'])->name('mproducts.all');
Route::get('mproducts/{mproduct}', [MproductController::class, 'getOne'])->name('mproducts.one');
Route::post('mproducts', [MproductController::class, 'add'])->name('mproducts.add');
Route::post('mproducts/{mproduct}', [MproductController::class, 'edit'])->name('mproducts.edit');
Route::delete('mproducts/{mproduct}', [MproductController::class, 'delete'])->name('mproducts.delete');
Route::post('mproducts/suggest', [MproductController::class, 'suggest'])->name('mproducts.suggest');


// Commission routes...
Route::get('commission', [CommissionController::class, 'getAll'])->name('commission.all');
Route::get('commission/{commission}', [CommissionController::class, 'getOne'])->name('commission.one');
Route::post('commission', [CommissionController::class, 'add'])->name('commission.add');
Route::post('commission/{commission}', [CommissionController::class, 'edit'])->name('commission.edit');
Route::delete('commission/{commission}', [CommissionController::class, 'delete'])->name('commission.delete');


// Staff routes...
Route::get('staff', [StaffController::class, 'getAll'])->name('staff.all');
Route::get('staff/{staff}', [StaffController::class, 'getOne'])->name('staff.one');
Route::post('staff', [StaffController::class, 'add'])->name('staff.add');
Route::post('staff/{staff}', [StaffController::class, 'edit'])->name('staff.edit');
Route::delete('staff/{staff}', [StaffController::class, 'delete'])->name('staff.delete');

// Client routes...
Route::get('clients', [ClientController::class, 'getAll'])->name('clients.all');
Route::get('clients/{client}', [ClientController::class, 'getOne'])->name('clients.one');
Route::post('clients', [ClientController::class, 'add'])->name('clients.add');
Route::post('clients/{client}', [ClientController::class, 'edit'])->name('clients.edit');
Route::delete('clients/{client}', [ClientController::class, 'delete'])->name('clients.delete');

// Broker routes...
Route::get('brokers', [BrokerController::class, 'getAll'])->name('brokers.all');
Route::get('brokers/{broker}', [BrokerController::class, 'getOne'])->name('brokers.one');
Route::post('brokers', [BrokerController::class, 'add'])->name('brokers.add');
Route::post('brokers/{broker}', [BrokerController::class, 'edit'])->name('brokers.edit');
Route::delete('brokers/{broker}', [BrokerController::class, 'delete'])->name('brokers.delete');

// Branch routes...
Route::get('branches', [BranchController::class, 'getAll'])->name('branches.all');
Route::get('branches/{branch}', [BranchController::class, 'getOne'])->name('branches.one');
Route::post('branches', [BranchController::class, 'add'])->name('branches.add');
Route::post('branches/{branch}', [BranchController::class, 'edit'])->name('branches.edit');
Route::delete('branches/{branch}', [BranchController::class, 'delete'])->name('branches.delete');

// Note routes...
Route::post('notes', [NoteController::class, 'add'])->name('notes.add');
Route::delete('notes/{note}', [NoteController::class, 'delete'])->name('notes.delete');

// Policy routes...
Route::get('policies', [PolicyController::class, 'getAll'])->name('policies.all');
Route::get('policies/{policy}', [PolicyController::class, 'getOne'])->name('policies.one');
Route::post('policies', [PolicyController::class, 'add'])->name('policies.add');
Route::post('policies/{policy}', [PolicyController::class, 'edit'])->name('policies.edit');
Route::delete('policies/{policy}', [PolicyController::class, 'delete'])->name('policies.delete');

// Mutualfund routes...
Route::get('mutualfunds', [MutualfundController::class, 'getAll'])->name('mutualfunds.all');
Route::get('mutualfunds/{mutualfund}', [MutualfundController::class, 'getOne'])->name('mutualfunds.one');
Route::post('mutualfunds', [MutualfundController::class, 'add'])->name('mutualfunds.add');
Route::post('mutualfunds/{mutualfund}', [MutualfundController::class, 'edit'])->name('mutualfunds.edit');
Route::delete('mutualfunds/{mutualfund}', [MutualfundController::class, 'delete'])->name('mutualfunds.delete');

// Attachment routes...
Route::delete('attachments/{attachment}', [AttachmentController::class, 'delete'])->name('attachments.delete');
Route::post('attachments', [AttachmentController::class, 'add'])->name('attachments.add');

// Payment routes...
Route::delete('payments/{payment}', [PaymentController::class, 'delete'])->name('payments.delete');
Route::post('payments', [PaymentController::class, 'add'])->name('payments.add');
Route::post('payments/update', [PaymentController::class, 'update'])->name('payments.update');

// Text routes...
Route::delete('texts/{text}', [TextController::class, 'delete'])->name('texts.delete');
Route::get('communication/texts/{recipient}', [TextController::class, 'getAll'])->name('texts.all');
Route::post('texts', [TextController::class, 'add'])->name('texts.add');

// Email routes...
Route::delete('emails/{email}', [EmailController::class, 'delete'])->name('emails.delete');
Route::get('communication/emails/{recipient}', [EmailController::class, 'getAll'])->name('emails.all');
Route::post('emails', [EmailController::class, 'add'])->name('emails.add');

// Inbox routes...
Route::get('inbox/{chatee?}', [InboxController::class, 'getAll'])->name('inbox.all');

// Chat routes...
Route::get('chats/live', [ChatController::class, 'live'])->name('chats.live');
Route::post('chats/see', [ChatController::class, 'see'])->name('chats.see');
Route::post('chats', [ChatController::class, 'send'])->name('chats.send');

// Reports routes...
Route::get('reports', [ReportController::class, 'get'])->name('reports.index');

// Communication routes...
Route::get('communication', [CommunicationController::class, 'get'])->name('communication');

// Update routes...
Route::get('update/{status}', [UpdateController::class, 'load']);
Route::get('update', [UpdateController::class, 'get']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
