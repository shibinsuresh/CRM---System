<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NoteController extends Controller
{
    /**
     * Map of allowed notable short-types to their model classes.
     */
    private const NOTABLE = [
        'contact' => Contact::class,
        'company' => Company::class,
        'deal' => Deal::class,
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'notable_type' => ['required', Rule::in(array_keys(self::NOTABLE))],
            'notable_id' => ['required', 'integer'],
            'body' => ['required', 'string'],
        ]);

        $model = self::NOTABLE[$data['notable_type']]::findOrFail($data['notable_id']);

        // Only users who can manage the parent record may add notes to it.
        $this->authorize('manage-record', $model);

        $model->notes()->create([
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Note added.');
    }

    public function destroy(Request $request, Note $note)
    {
        abort_unless(
            $request->user()->canManageAll() || $note->user_id === $request->user()->id,
            403
        );

        $note->delete();

        return redirect()->back()->with('success', 'Note deleted.');
    }
}
