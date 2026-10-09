<?php

namespace App\Http\Requests\Admin;

use App\Enums\MediaType;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Rules\YouTubeLink;
use App\Support\YouTube;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-site') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Project|null $project */
        $project = $this->route('project');
        $imageKb = (int) config('sparklenz.max_image_kb');

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:180', 'alpha_dash:ascii', Rule::unique('projects', 'slug')->ignore($project)],
            'client_label' => ['nullable', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:120'],
            'summary' => ['required', 'string', 'max:400'],
            'description' => ['nullable', 'string', 'max:3000'],
            'media_type' => ['required', new Enum(MediaType::class)],
            'youtube_url' => ['nullable', 'string', 'max:300', new YouTubeLink],
            'video_orientation' => ['required', Rule::in(['landscape', 'portrait'])],
            'cover' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max($imageKb)
                ->dimensions(Rule::dimensions()->minWidth(800)->minHeight(450)->maxWidth(8000)->maxHeight(8000))],
            'cover_alt' => ['nullable', 'string', 'max:250', Rule::requiredIf(fn (): bool => $this->hasFile('cover') || ($project?->cover_path && ! $this->boolean('remove_cover')))],
            'remove_cover' => ['boolean'],
            'teaser' => ['nullable', File::types(['mp4', 'webm'])->max((int) config('sparklenz.max_teaser_kb')), 'mimetypes:video/mp4,video/webm'],
            'remove_teaser' => ['boolean'],
            'gallery' => ['array', 'max:12'],
            'gallery.*' => [File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max($imageKb)
                ->dimensions(Rule::dimensions()->minWidth(600)->minHeight(400)->maxWidth(8000)->maxHeight(8000))],
            'gallery_alt' => ['array'],
            'gallery_alt.*' => ['nullable', 'string', 'max:250'],
            'media' => ['array'],
            'media.*.alt' => ['required', 'string', 'max:250'],
            'media.*.caption' => ['nullable', 'string', 'max:250'],
            'media.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_featured' => ['boolean'],
            'is_demo' => ['boolean'],
            'status' => ['required', new Enum(ProjectStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cover.dimensions' => 'The cover must be at least 800 × 450 pixels.',
            'gallery.*.dimensions' => 'Gallery images must be at least 600 × 400 pixels.',
            'cover_alt.required' => 'Describe the cover image for people using screen readers.',
            'media.*.alt.required' => 'Every gallery image needs a description.',
            'teaser.mimetypes' => 'Teasers must be MP4 or WebM video files.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach (array_keys($this->file('gallery', [])) as $index) {
                    if (blank($this->input("gallery_alt.$index"))) {
                        $validator->errors()->add("gallery_alt.$index", 'Describe each new gallery image.');
                    }
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) ($this->input('slug') ?: $this->input('title'))),
            'is_featured' => $this->boolean('is_featured'),
            'is_demo' => $this->boolean('is_demo'),
            'remove_cover' => $this->boolean('remove_cover'),
            'remove_teaser' => $this->boolean('remove_teaser'),
        ]);
    }

    public function youtubeId(): ?string
    {
        return YouTube::extractId($this->input('youtube_url'));
    }
}
