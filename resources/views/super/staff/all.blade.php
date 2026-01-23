@extends('templates.staff.all')

@section('modals')
    <!-- new staff modal -->
    <div class="ui tiny modal" id="newStaffModal">
        <div class="header">{{ trans('staff.modal.header.new') }}</div>
        <div class="content">
            <p>{{ trans('staff.modal.instruction.new') }}</p>
            <form action="{{ route('staff.add') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="two fields">
                        <div class="field required">
                            <label>{{ trans('staff.input.label.first_name') }}</label>
                            <input type="text" name="first_name" placeholder="{{ trans('staff.input.placeholder.first_name') }}" required value="{{ old('first_name') }}"/>
                        </div>
                        <div class="field">
                            <label>{{ trans('staff.input.label.last_name') }}</label>
                            <input type="text" name="last_name" placeholder="{{ trans('staff.input.placeholder.last_name') }}" value="{{ old('last_name') }}"/>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field required">
                            <label>{{ trans('staff.input.label.email') }}</label>
                            <input type="email" name="email" placeholder="{{ trans('staff.input.placeholder.email') }}" required value="{{ old('email') }}"/>
                        </div>
                        <div class="field">
                            <label>{{ trans('staff.input.label.phone') }}</label>
                            <input type="tel" placeholder="{{ trans('staff.input.placeholder.phone') }}" value="{{ old('phone') }}"/>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>{{ trans('staff.input.label.birthday') }}</label>
                            <input type="text" class="datepicker" name="birthday" placeholder="{{ trans('staff.input.placeholder.birthday') }}" value="{{ old('birthday') }}"/>
                        </div>

                        <div class="field required">
                            <label>{{ trans('staff.input.label.branches') }}</label>
                            <select class="dropdown fluid search ui" name="branch_id" required>
                                @foreach($branches as $branch)
                                <option{{ !empty(old('branch_id')) && old('branch_id') === $branch->id  ? ' selected' : '' }} value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field required">
                            <label>Staff Code</label>
                            <input type="text" name="staff_code" placeholder="Staff Code" required value="{{ old('staff_code')}}"/>
                        </div>
                        <div class="field">
                            <label>Age</label>
                            <input type="number" name="age" placeholder="Age" value="{{ old('age') }}"/>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field ">
                            <label>Date of Join</label>
                            <input type="text" class="datepicker" name="doj" placeholder="Date of Join" value="{{ old('doj') }}"/>
                        </div>
                        <div class="field required">
                            <label>Role</label>
                            <select class="dropdown fluid search ui" name="staff_role" required>
                                <option value="BM">Branch Manger</option>
                                <option value="S">Staff</option>
                                <option value="BA">Branch Assist</option>
                            </select>
                        </div>

                    </div>
                    <div class="two fields">
                        <div class="field required" style="display: none;">
                            <label>{{ trans('staff.input.label.company') }}</label>
                            <select class="dropdown fluid search ui" name="company_id" required>
                                @foreach($companies as $company)
                                <option{{ !empty(old('company_id')) && old('company_id') === $company->id || empty(old('company_id')) && $company === $user->company ? ' selected' : '' }} value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field required" style="display: none;">
                            <label>{{ trans('staff.input.label.commission_rate') }} (%)</label>
                            <input type="number" max="100" min="0" name="commission_rate" placeholder="{{ trans('staff.input.placeholder.commission_rate') }}" required step="0.01" value="{{ old('commission_rate') ?: 0 }}"/>
                        </div>





                    </div>


                    <div class="field">
                        <label>{{ trans('staff.input.label.address') }}</label>

                        <textarea name="address" rows="4">{{ old('address') }}</textarea>
                    </div>


                    <div class="field">
                        <label>{{ trans('staff.input.label.profile_image') }}</label>
                        <input type="file" accept="image/*" data-allowed-file-extensions="bmp gif jpeg jpg png svg" class="file-upload" data-default-file="{{ asset('uploads/images/users/default-profile.jpg') }}" name="profile_image"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui buttons">
                <button class="ui cancel button">{{ trans('staff.modal.button.cancel.new') }}</button>
                <div class="or" data-text="{{ trans('products.modal.button.or') }}"></div>
                <button class="ui positive primary button">{{ trans('staff.modal.button.confirm.new') }}</button>
            </div>
        </div>
    </div>
@endsection
