<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ContactInbox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.contacts.index', [
            'messages' => ContactInbox::all(),
            'stats' => ContactInbox::stats(),
        ]);
    }

    public function show(string $id): View
    {
        $message = ContactInbox::find($id);
        abort_if($message === null, 404);

        return view('admin.contacts.show', [
            'message' => $message,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:new,in_progress,replied,archived'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $updated = ContactInbox::update($id, [
            'status' => $request->input('status'),
            'admin_note' => $request->input('admin_note', ''),
        ]);

        abort_if($updated === null, 404);

        return redirect()->route('admin.contacts.show', $id)->with('success', 'Message updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $message = ContactInbox::find($id);
        abort_if($message === null, 404);

        ContactInbox::delete($id);

        return redirect()->route('admin.contacts.index')->with('success', 'Message deleted successfully.');
    }
}
