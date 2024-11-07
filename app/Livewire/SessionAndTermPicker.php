<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Session;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

class SessionAndTermPicker extends Component
{
    public $sessions = [];
    public $currentSession;
    public $currentTerm;

    public function mount()
    {

    }

    /**
     * Get all the sessions and terms and map them to the sessions array.
     *
     * @return void
     */
    public function setSessionAndTermMapping()
    {
        if (Cache::has('session_term_picker')) {
            $this->sessions = Cache::get('session_term_picker');
        } else {
            Session::where('school_id', auth()->user()->school_id)->get()->each(function ($session) {

                if (!$session) {
                    return $this->sessions;
                }

                $terms = $session->terms()->get();

                if (!$terms) {
                    return $this->sessions;
                }

                $currentlyActiveSession = Session::active(auth()->user()->school_id);

                if (!$currentlyActiveSession) {
                    return $this->sessions;
                }

                $currentlyActiveTerm = $currentlyActiveSession->terms()->where('active', true)->firstOrFail();

                $terms->each(function ($term) use ($session, $currentlyActiveTerm, $currentlyActiveSession) {
                    $currentText = $term->id === $currentlyActiveTerm->id && $session->id === $currentlyActiveSession->id ? '(Current) ' : '';
                    $this->sessions['term_'.$term->id.'_session_'.$session->id] = $currentText . $session->year .' '. $term->name;
                });
            });

            Cache::put('session_term_picker', $this->sessions);
        }
    }

    public function setCurrentSession($term_session_id)
    {
        $term_session = explode('_', $term_session_id);
        $term = Term::where('id', $term_session[1])
                    ->where('school_id', auth()->user()->school_id)
                    ->firstOrFail();

        $session = Session::where('id', $term_session[3])
                            ->where('school_id', auth()->user()->school_id)
                            ->firstOrFail();

        $this->currentSession = $session;
        $this->currentTerm = $term;

        session()->put('currentSession', $session);
        session()->put('currentTerm', $term);

        $this->setSessionAndTermMapping();

        $this->redirect(route('filament.admin.pages.dashboard'));
    }

    public function render()
    {
        if (!Auth::check()) {
            return;
        }

        $this->currentSession = session()->has('currentSession') ? session()->get('currentSession') : Session::active(auth()->user()->school_id);

        if (!$this->currentSession) {
            $this->currentSession = Session::active(auth()->user()->school_id);
        }

        $this->currentTerm = session()->has('currentTerm') ? session()->get('currentTerm') : $this->currentSession->terms()->where('active', true)->firstOrFail();

        if (!$this->currentTerm) {
            $this->currentTerm = Term::where('school_id', auth()->user()->school_id)
                                        ->where('active', true)
                                        ->where('session_id', $this->currentSession->id)
                                        ->firstOrFail();
        }

        $this->setSessionAndTermMapping();

        Log::debug('Current session and term: ' . print_r([$this->currentSession, $this->currentTerm], true));
        return view('livewire.session-and-term-picker');
    }
}
