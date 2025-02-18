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
        $this->loadCurrentSessionAndTerm();
        $this->setSessionAndTermMapping();
    }

    protected function loadCurrentSessionAndTerm()
    {
        if (!Auth::check()) {
            return;
        }

        // Get current session
        if (session()->has('currentSession')) {
            $this->currentSession = session()->get('currentSession');
        } else {
            $this->currentSession = Session::active(auth()->user()->school_id);
            session()->put('currentSession', $this->currentSession);
        }

        // Get current term
        if (session()->has('currentTerm')) {
            $this->currentTerm = session()->get('currentTerm');
        } else if ($this->currentSession) {
            $this->currentTerm = $this->currentSession->terms()->where('active', true)->first();
            session()->put('currentTerm', $this->currentTerm);
        }

        // Fallback if no session/term is set
        if (!$this->currentSession || !$this->currentTerm) {
            $this->resetToActiveSessionAndTerm();
        }
    }

    protected function resetToActiveSessionAndTerm()
    {
        $this->currentSession = Session::active(auth()->user()->school_id);
        if ($this->currentSession) {
            $this->currentTerm = $this->currentSession->terms()->where('active', true)->first();

            session()->put('currentSession', $this->currentSession);
            session()->put('currentTerm', $this->currentTerm);
        }
    }

    /**
     * Get all the sessions and terms and map them to the sessions array.
     *
     * @return void
     */
    public function setSessionAndTermMapping()
    {
        $cacheKey = 'session_term_picker_' . auth()->id();

        if (Cache::has($cacheKey)) {
            $this->sessions = Cache::get($cacheKey);
            return;
        }

        $this->sessions = [];
        $activeSession = Session::active(auth()->user()->school_id);
        $activeTerm = $activeSession ? $activeSession->terms()->where('active', true)->first() : null;

        Session::where('school_id', auth()->user()->school_id)
            ->orderBy('year', 'desc')
            ->get()
            ->each(function ($session) use ($activeSession, $activeTerm) {
                $terms = $session->terms()->orderBy('id')->get();

                foreach ($terms as $term) {
                    $isCurrentlyActive = ($activeSession && $activeTerm) &&
                                       ($term->id === $activeTerm->id && $session->id === $activeSession->id);

                    $currentText = $isCurrentlyActive ? '(Current) ' : '';
                    $key = "term_{$term->id}_session_{$session->id}";
                    $this->sessions[$key] = $currentText . $session->year . ' ' . $term->name;
                }
            });

        Cache::put($cacheKey, $this->sessions, now()->addMinutes(30));
    }

    public function setCurrentSession($term_session_id)
    {
        try {
            $parts = explode('_', $term_session_id);
            if (count($parts) !== 4) {
                throw new \Exception('Invalid session/term format');
            }

            $termId = $parts[1];
            $sessionId = $parts[3];

            $term = Term::where('id', $termId)
                ->where('school_id', auth()->user()->school_id)
                ->first();

            $session = Session::where('id', $sessionId)
                ->where('school_id', auth()->user()->school_id)
                ->first();

            if (!$term || !$session) {
                throw new \Exception('Term or session not found');
            }

            $this->currentSession = $session;
            $this->currentTerm = $term;

            session()->put('currentSession', $session);
            session()->put('currentTerm', $term);

            // Clear any cached data that might depend on session/term
            Cache::forget('session_term_picker_' . auth()->id());
            $this->setSessionAndTermMapping();

            return redirect()->to(route('filament.admin.pages.dashboard'));
        } catch (\Exception $e) {
            Log::error('Session picker error: ' . $e->getMessage());
            $this->resetToActiveSessionAndTerm();
        }
    }

    public function render()
    {
        if (!Auth::check()) {
            return;
        }

        $this->loadCurrentSessionAndTerm();
        return view('livewire.session-and-term-picker');
    }
}
