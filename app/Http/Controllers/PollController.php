<?php

namespace App\Http\Controllers;

use App\Mail\PollPublishedMail;
use App\Models\Answer;
use App\Models\Poll;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PollController extends Controller
{
    public function index(): View
    {
        $polls = Poll::where("is_active", true)
            ->where(function ($q) {
                $q->whereNull("start_date")->orWhere("start_date", "<=", now());
            })
            ->where(function ($q) {
                $q->whereNull("end_date")->orWhere("end_date", ">=", now());
            })
            ->withCount("questions")
            ->latest()
            ->paginate(10);

        return view("polls.index", compact("polls"));
    }

    public function show(Poll $poll): View
    {
        if (!$poll->isActive()) {
            abort(404);
        }

        $hasVoted = Answer::where("user_id", Auth::id())
            ->whereIn("question_id", $poll->questions()->pluck("id"))
            ->exists();

        if ($hasVoted) {
            return to_route("polls.results", $poll);
        }

        return view("polls.vote", compact("poll"));
    }

    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        if (!$poll->isActive()) {
            return back()->with("error", 'Ce sondage n\'est plus actif.');
        }

        $questions = $poll->questions()->with("options")->get();

        foreach ($questions as $question) {
            $hasVoted = Answer::where("user_id", Auth::id())
                ->where("question_id", $question->id)
                ->exists();

            if ($hasVoted) {
                return back()->with(
                    "error",
                    "Vous avez déjà voté pour ce sondage.",
                );
            }
        }

        $rules = [];
        foreach ($questions as $question) {
            $key = "question_{$question->id}";
            $rule = [];

            if ($question->is_required) {
                $rule[] = "required";
            } else {
                $rule[] = "nullable";
            }

            if ($question->type === "text_short") {
                $rule[] = "string";
                $rule[] = "max:1000";
            } elseif ($question->type === "unique_choice") {
                $rule[] = "exists:question_options,id";
            } elseif ($question->type === "multiple_choice") {
                $rule[] = "array";
                $rule[] = "exists:question_options,id";
            }

            $rules[$key] = $rule;
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($questions, $data, $poll) {
            foreach ($questions as $question) {
                $key = "question_{$question->id}";
                $value = $data[$key] ?? null;

                if ($value === null || $value === "") {
                    continue;
                }

                if ($question->type === "text_short") {
                    Answer::create([
                        "user_id" => Auth::id(),
                        "question_id" => $question->id,
                        "text_response" => $value,
                    ]);
                } elseif ($question->type === "unique_choice") {
                    $answer = Answer::create([
                        "user_id" => Auth::id(),
                        "question_id" => $question->id,
                        "text_response" => null,
                    ]);
                    $answer->options()->attach($value);
                } elseif ($question->type === "multiple_choice") {
                    $answer = Answer::create([
                        "user_id" => Auth::id(),
                        "question_id" => $question->id,
                        "text_response" => null,
                    ]);
                    $answer->options()->attach($value);
                }
            }
        });

        return to_route("polls.results", $poll)->with(
            "success",
            "Votre vote a été enregistré.",
        );
    }

    public function results(Poll $poll): View
    {
        $questions = $poll
            ->questions()
            ->with(["options", "answers"])
            ->get();

        $stats = [];
        foreach ($questions as $question) {
            $totalParticipants = Answer::where("question_id", $question->id)
                ->distinct("user_id")
                ->count("user_id");

            $optionStats = [];
            foreach ($question->options as $option) {
                $count = DB::table("answer_option")
                    ->where("question_option_id", $option->id)
                    ->count();
                $optionStats[] = [
                    "option" => $option,
                    "count" => $count,
                    "percentage" =>
                        $totalParticipants > 0
                            ? round(($count / $totalParticipants) * 100)
                            : 0,
                ];
            }

            $stats[] = [
                "question" => $question,
                "total_participants" => $totalParticipants,
                "option_stats" => $optionStats,
            ];
        }

        $totalVoters = Answer::whereIn("question_id", $questions->pluck("id"))
            ->distinct("user_id")
            ->count("user_id");

        return view("polls.results", compact("poll", "stats", "totalVoters"));
    }

    public function adminIndex(): View
    {
        $polls = Poll::withCount("questions")->latest()->paginate(20);

        return view("admin.polls.index", compact("polls"));
    }

    public function create(): View
    {
        return view("admin.polls.form");
    }

    public function storePoll(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "title" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "is_active" => ["boolean"],
            "start_date" => ["nullable", "date"],
            "end_date" => ["nullable", "date", "after_or_equal:start_date"],
        ]);

        $isActive = $request->boolean("is_active");

        $poll = Poll::create([
            "title" => $data["title"],
            "description" => $data["description"] ?? null,
            "is_active" => $isActive,
            "start_date" => $data["start_date"] ?? null,
            "end_date" => $data["end_date"] ?? null,
        ]);

        return to_route("admin.polls.edit", $poll)->with(
            "success",
            "Sondage créé. Ajoutez maintenant des questions.",
        );
    }

    public function edit(Poll $poll): View
    {
        $poll->load("questions.options");

        return view("admin.polls.form", compact("poll"));
    }

    public function update(Request $request, Poll $poll): RedirectResponse
    {
        $data = $request->validate([
            "title" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "is_active" => ["boolean"],
            "start_date" => ["nullable", "date"],
            "end_date" => ["nullable", "date", "after_or_equal:start_date"],
        ]);

        $wasActive = $poll->is_active;
        $isActive = $request->boolean("is_active");

        $poll->update([
            "title" => $data["title"],
            "description" => $data["description"] ?? null,
            "is_active" => $isActive,
            "start_date" => $data["start_date"] ?? null,
            "end_date" => $data["end_date"] ?? null,
        ]);

        if (!$wasActive && $isActive && $poll->questions()->exists()) {
            $this->notifyPollSubscribers($poll);
        }

        return back()->with("success", "Sondage mis à jour.");
    }

    public function destroyPoll(Poll $poll): RedirectResponse
    {
        $poll->delete();

        return to_route("admin.polls.index")->with(
            "success",
            "Sondage supprimé.",
        );
    }

    public function storeQuestion(
        Request $request,
        Poll $poll,
    ): RedirectResponse {
        $data = $request->validate([
            "text" => ["required", "string", "max:255"],
            "type" => [
                "required",
                "in:text_short,unique_choice,multiple_choice,scale",
            ],
            "is_required" => ["boolean"],
            "options" => ["nullable", "array"],
            "options.*" => ["string", "max:255"],
            "scale_min" => ["required_if:type,scale", "integer", "min:1"],
            "scale_max" => [
                "required_if:type,scale",
                "integer",
                "min:1",
                "max:10",
            ],
        ]);

        $type = $data["type"] === "scale" ? "unique_choice" : $data["type"];
        $maxOrder = $poll->questions()->max("order_index") ?? -1;

        $question = Question::create([
            "poll_id" => $poll->id,
            "text" => $data["text"],
            "type" => $type,
            "is_required" => $request->boolean("is_required"),
            "order_index" => $maxOrder + 1,
        ]);

        if ($data["type"] === "scale") {
            for ($i = $data["scale_min"]; $i <= $data["scale_max"]; $i++) {
                QuestionOption::create([
                    "question_id" => $question->id,
                    "text" => (string) $i,
                    "order_index" => $i,
                ]);
            }
        } elseif (!empty($data["options"])) {
            foreach ($data["options"] as $index => $optionText) {
                if (trim($optionText) !== "") {
                    QuestionOption::create([
                        "question_id" => $question->id,
                        "text" => $optionText,
                        "order_index" => $index,
                    ]);
                }
            }
        }

        return back()->with("success", "Question ajoutée.");
    }

    public function destroyQuestion(Question $question): RedirectResponse
    {
        $question->delete();

        return back()->with("success", "Question supprimée.");
    }

    private function notifyPollSubscribers(Poll $poll): void
    {
        $subscribers = User::where("newsletter_polls", true)
            ->where("is_active", true)
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber)->queue(
                new PollPublishedMail($poll, $subscriber),
            );
        }
    }
}
