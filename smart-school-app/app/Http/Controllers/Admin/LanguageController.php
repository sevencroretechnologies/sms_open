<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\LanguageService;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Language Controller
 * 
 * Prompt 496: Add Language Management UI
 * 
 * Manages language settings and translations for multi-language support.
 */
class LanguageController extends Controller
{
    protected LanguageService $languageService;
    protected TranslationService $translationService;

    public function __construct(LanguageService $languageService, TranslationService $translationService)
    {
        $this->languageService = $languageService;
        $this->translationService = $translationService;
    }

    /**
     * Display a listing of languages.
     */
    public function index()
    {
        $languages = $this->languageService->getAvailableLanguages(false);
        
        return view('admin.settings.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new language.
     */
    public function create()
    {
        return view('admin.settings.languages.create');
    }

    /**
     * Store a newly created language.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:5|unique:languages,code',
            'name' => 'required|string|max:100',
            'native_name' => 'required|string|max:100',
            'flag_code' => 'nullable|string|max:5',
            'is_rtl' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            $language = $this->languageService->createLanguage([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'native_name' => $validated['native_name'],
                'flag_code' => $validated['flag_code'] ?? null,
                'is_rtl' => $request->boolean('is_rtl'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()
                ->route('admin.settings.languages.index')
                ->with('success', __('Language created successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to create language', ['error' => $e->getMessage()]);
            return back()->with('error', __('Failed to create language.'));
        }
    }

    /**
     * Show the form for editing a language.
     */
    public function edit(Language $language)
    {
        $translationCount = $this->translationService->getAll($language->code)->count();
        $translatedCount = $translationCount;
        $missingCount = 0;

        // Calculate missing translations compared to default locale
        $defaultLocale = config('app.locale', 'en');
        if ($language->code !== $defaultLocale) {
            $missing = $this->translationService->findMissing($defaultLocale, $language->code);
            $missingCount = count($missing);
        }

        return view('admin.settings.languages.edit', compact(
            'language',
            'translationCount',
            'translatedCount',
            'missingCount'
        ));
    }

    /**
     * Update the specified language.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'native_name' => 'required|string|max:100',
            'flag_code' => 'nullable|string|max:5',
            'is_rtl' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            $this->languageService->updateLanguage($language, [
                'name' => $validated['name'],
                'native_name' => $validated['native_name'],
                'flag_code' => $validated['flag_code'] ?? null,
                'is_rtl' => $request->boolean('is_rtl'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()
                ->route('admin.settings.languages.index')
                ->with('success', __('Language updated successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to update language', ['error' => $e->getMessage()]);
            return back()->with('error', __('Failed to update language.'));
        }
    }

    /**
     * Remove the specified language.
     */
    public function destroy(Language $language)
    {
        try {
            $this->languageService->deleteLanguage($language);

            return redirect()
                ->route('admin.settings.languages.index')
                ->with('success', __('Language deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to delete language', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle language active status.
     */
    public function toggle(Language $language)
    {
        try {
            if ($language->is_active) {
                $this->languageService->deactivateLanguage($language);
                $message = __('Language deactivated successfully.');
            } else {
                $this->languageService->activateLanguage($language);
                $message = __('Language activated successfully.');
            }

            return redirect()
                ->route('admin.settings.languages.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Failed to toggle language', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show translations for a language.
     */
    public function translations(Language $language)
    {
        $translations = $this->translationService->getGrouped($language->code);
        $groups = $this->translationService->getGroups();

        return view('admin.settings.languages.translations', compact(
            'language',
            'translations',
            'groups'
        ));
    }

    /**
     * Update a translation.
     */
    public function updateTranslation(Request $request, Language $language)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
            'group' => 'required|string',
        ]);

        try {
            $this->translationService->set(
                $validated['key'],
                $validated['value'],
                $language->code,
                $validated['group']
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Export translations.
     */
    public function export(Request $request)
    {
        $locale = $request->get('locale', config('app.locale'));
        $group = $request->get('group');

        $translations = $this->translationService->export($locale, $group);

        $filename = "translations_{$locale}" . ($group ? "_{$group}" : '') . '.json';

        return response()->json($translations)
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        $languages = $this->languageService->getAvailableLanguages(false);
        return view('admin.settings.languages.import', compact('languages'));
    }

    /**
     * Import translations.
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:json',
            'locale' => 'required|string',
            'group' => 'required|string',
            'overwrite' => 'boolean',
        ]);

        try {
            $result = $this->translationService->importFromJson(
                $request->file('file')->getPathname(),
                $validated['locale'],
                $validated['group'],
                $request->boolean('overwrite')
            );

            return redirect()
                ->route('admin.settings.languages.index')
                ->with('success', __('Imported :imported translations, skipped :skipped.', $result));
        } catch (\Exception $e) {
            Log::error('Failed to import translations', ['error' => $e->getMessage()]);
            return back()->with('error', __('Failed to import translations: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Sync missing translation keys.
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'source_locale' => 'required|string',
        ]);

        try {
            $languages = $this->languageService->getAvailableLanguages(true);
            $totalCopied = 0;

            foreach ($languages as $language) {
                if ($language->code !== $validated['source_locale']) {
                    $result = $this->translationService->copyTranslations(
                        $validated['source_locale'],
                        $language->code,
                        null,
                        false
                    );
                    $totalCopied += $result['copied'];
                }
            }

            return redirect()
                ->route('admin.settings.languages.index')
                ->with('success', __('Synced :count missing translation keys.', ['count' => $totalCopied]));
        } catch (\Exception $e) {
            Log::error('Failed to sync translations', ['error' => $e->getMessage()]);
            return back()->with('error', __('Failed to sync translations.'));
        }
    }
}
