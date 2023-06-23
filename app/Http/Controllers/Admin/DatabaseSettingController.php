<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;


class DatabaseSettingController extends Controller
{
    public function db_index()
    {

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $filter_tables = array('module_types','admin_roles','admins','business_settings','colors','currencies',
                                'failed_jobs','migrations','oauth_access_tokens','oauth_auth_codes',
                                'oauth_clients','oauth_personal_access_clients','oauth_refresh_tokens',
                                'password_resets','personal_access_tokens','phone_or_email_verifications',
                                'social_medias','soft_credentials','users','jobs');
        $tables = array_values(array_diff($tables, $filter_tables));

        $rows = [];
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            array_push($rows, $count);
        }

        return view('admin-views.business-settings.db-index', compact('tables', 'rows'));
    }
    public function clean_db(Request $request)
    { 
        if (env('APP_MODE') == 'demo') {
            Toastr::info(translate('messages.update_option_is_disable_for_demo'));
            return back();
        }

        $tables = (array)$request->tables;

        if(count($tables) == 0) {
            Toastr::error(translate('No Table Updated'));
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            Toastr::error(translate('Failed to update!'));
            return back();
        }

        Toastr::success(translate('messages.updated_successfully'));
        return back();
    }
}
