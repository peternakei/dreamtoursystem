<?php

namespace Database\Seeders\Support;

use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\Attachments\AttachmentType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Shared helper for the public-site content seeders.
 *
 *  - Resolves the manifest of source images (frontend `/public/assets/images/`
 *    and pre-existing backend `public/storage/uploads/` fixtures).
 *  - Invokes the Node-side WebP converter (database/seeders/Support/convert-public-assets.mjs)
 *    once per seeder run so each target only has to be processed when its
 *    source is newer.
 *  - Registers polymorphic Attachment rows pointing at the converted file.
 *
 * Seeders should call `convertAll()` once near the top of `run()` and then
 * `attach($model, $filename)` as they save each content row.
 */
class PublicAssetImporter
{
    public const SOURCE_FRONTEND = 'frontend';
    public const SOURCE_STORAGE = 'storage';

    /**
     * Single source of truth for what gets imported.
     *
     *   target_filename.webp => ['from' => 'frontend'|'storage', 'path' => 'relative/path.ext']
     *
     *  - `frontend` paths are resolved against the Next.js project's
     *    `public/assets/images/` directory. Defaults to the sibling
     *    `serenbluesafaris/` checkout; override with SBS_FRONTEND_ASSETS_PATH.
     *  - `storage` paths are resolved against this app's
     *    `public/storage/uploads/` (existing fixtures).
     */
    public const MANIFEST = [
        // Categories ------------------------------------------------------
        'category-first-time-safaris.webp' => ['from' => self::SOURCE_FRONTEND, 'path' => 'Journey/first-time-safaris.png'],
        'category-honeymoon.webp'          => ['from' => self::SOURCE_FRONTEND, 'path' => 'Journey/honey-moon.PNG'],
        'category-safari-zanzibar.webp'    => ['from' => self::SOURCE_FRONTEND, 'path' => 'Journey/safari & zanzibar.jpeg'],
        'category-luxury.webp'             => ['from' => self::SOURCE_FRONTEND, 'path' => 'Journey/luxury-safari.jpeg'],
        'category-family.webp'             => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/elephants.jpg'],

        // Destinations ----------------------------------------------------
        'destination-serengeti.webp'  => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/girrafe.jpeg'],
        'destination-tarangire.webp'  => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/luxury-safari.jpeg'],
        'destination-mikumi.webp'     => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/mikumi.jpeg'],
        'destination-ngorongoro.webp' => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/safaris.jpeg'],
        'destination-selous.webp'     => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/selous.jpeg'],
        'destination-ruaha.webp'      => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/zebra.jpg'],
        'destination-zanzibar.webp'   => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/stone-town.jpg'],

        // Gallery shots (re-used across destinations & trips) -------------
        'gallery-elephants.webp'     => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/elephants.jpg'],
        'gallery-mnemba.webp'        => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/mnemba-island.jpg'],
        'gallery-nungwi.webp'        => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/nungwi-beach.jpg'],
        'gallery-jozani.webp'        => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/jozani-forest.jpg'],
        'gallery-spice-farms.webp'   => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/spice-farms.jpg'],
        'gallery-kendwa.webp'        => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/kendwa-beach.jpg'],
        'gallery-prison-island.webp' => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/prison-island.jpg'],
        'gallery-nakupenda.webp'     => ['from' => self::SOURCE_FRONTEND, 'path' => 'Zanzibar/nakupenda-sandbank.jpg'],
        'gallery-maasai.webp'        => ['from' => self::SOURCE_FRONTEND, 'path' => 'Package/maasai.jpg'],
        'gallery-luxury-lodge.webp'  => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/luxury-safari.jpeg'],
        'gallery-zebra.webp'         => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/zebra.jpg'],
        'gallery-girrafe.webp'       => ['from' => self::SOURCE_FRONTEND, 'path' => 'Safaris/girrafe.jpeg'],

        // Trip covers -----------------------------------------------------
        'trip-mikumi-cover.webp'          => ['from' => self::SOURCE_FRONTEND, 'path' => 'Package/mikumi.jpeg'],
        'trip-selous-cover.webp'          => ['from' => self::SOURCE_FRONTEND, 'path' => 'Package/selous.jpeg'],
        'trip-safari-zanzibar-cover.webp' => ['from' => self::SOURCE_FRONTEND, 'path' => 'Package/safari & zanzibar.jpeg'],

        // Accommodations (existing JPEG fixtures -> WebP) -----------------
        'accommodation-tarangire-treetops.webp'      => ['from' => self::SOURCE_STORAGE, 'path' => '1648791662540000_0.jpeg'],
        'accommodation-ngorongoro-crater-lodge.webp' => ['from' => self::SOURCE_STORAGE, 'path' => '1653710809150600_0.jpeg'],
        'accommodation-serengeti-migration-camp.webp'=> ['from' => self::SOURCE_STORAGE, 'path' => '1653726716609500_0.jpeg'],

        // Vehicles ---------------------------------------------------------
        // Source photos live in public/storage/uploads/ with `-src.jpg`
        // suffixes (any image format sharp can read works). The .webp
        // outputs use stable filenames so Attachment rows survive
        // re-converts unchanged.
        'vehicle-landcruiser.webp' => ['from' => self::SOURCE_STORAGE, 'path' => 'vehicle-landcruiser-src.jpg'],
        'vehicle-game-viewer.webp' => ['from' => self::SOURCE_STORAGE, 'path' => 'vehicle-game-viewer-src.jpg'],
        'vehicle-open-top.webp'    => ['from' => self::SOURCE_STORAGE, 'path' => 'vehicle-open-top-src.jpg'],
        'vehicle-tour-van.webp'    => ['from' => self::SOURCE_STORAGE, 'path' => '1653770449225000_0.jpeg'],
    ];

    private ?Command $command;
    private bool $alreadyConverted = false;
    private ?int $cachedAttachmentTypeId = null;

    public function __construct(?Command $command = null)
    {
        $this->command = $command;
    }

    /**
     * Resolve a source entry from the manifest to an absolute filesystem path.
     */
    public function resolveSource(string $target): ?string
    {
        if (!isset(self::MANIFEST[$target])) {
            return null;
        }

        return $this->resolveEntry(self::MANIFEST[$target]);
    }

    /**
     * Absolute path the WebP file should live at (whether or not it exists yet).
     */
    public function targetPath(string $target): string
    {
        return public_path('storage/uploads/' . $target);
    }

    /**
     * Run the Node converter once per seeder process. Subsequent calls are no-ops.
     */
    public function convertAll(): void
    {
        if ($this->alreadyConverted) {
            return;
        }
        $this->alreadyConverted = true;

        $entries = [];
        foreach (self::MANIFEST as $target => $entry) {
            $src = $this->resolveEntry($entry);
            $entries[] = [
                'src' => $src,
                'dst' => $this->targetPath($target),
            ];
        }

        $manifestPath = storage_path('app/public-asset-manifest.json');
        @mkdir(dirname($manifestPath), 0775, true);
        file_put_contents($manifestPath, json_encode($entries, JSON_PRETTY_PRINT));

        $script = __DIR__ . DIRECTORY_SEPARATOR . 'convert-public-assets.mjs';
        $env = [];

        // Frontend node_modules holds sharp on the operator's machine.
        $frontendNodeModules = env('SBS_NODE_MODULES_PATH', base_path('../../serenbluesafaris/node_modules'));
        if (is_dir($frontendNodeModules)) {
            $env['SBS_NODE_MODULES_PATH'] = $frontendNodeModules;
        }

        $process = new Process(['node', $script, $manifestPath], base_path(), $env + getenv());
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            if ($this->command) {
                $this->command->getOutput()->write($buffer);
            } else {
                fwrite(STDOUT, $buffer);
            }
        });

        @unlink($manifestPath);

        if (!$process->isSuccessful()) {
            $this->warn('Asset conversion completed with non-zero exit (' . $process->getExitCode() . '). Continuing — attachments will be skipped for missing files.');
        }
    }

    /**
     * Register (or refresh) a polymorphic Attachment row pointing at one of
     * the converted WebP files. No-ops gracefully when the WebP isn't on disk
     * yet so a partial conversion still produces consistent data.
     */
    public function attach(
        Model $model,
        string $target,
        string $role = Attachment::ROLE_COVER,
        int $sortOrder = 0,
        ?string $title = null
    ): ?Attachment {
        if (!is_file($this->targetPath($target))) {
            $this->warn("attach: target missing on disk, skipped — {$target}");
            return null;
        }

        $userId = (int) ($model->created_by ?: 1);

        return Attachment::firstOrCreate(
            [
                'attachmentable_type' => $model::class,
                'attachmentable_id' => $model->id,
                'name' => $target,
            ],
            [
                // `path` is NOT NULL; the model's getUrlAttribute() prefers
                // `name` (resolving to /storage/uploads/{name}) but we still
                // populate `path` with the same URL so legacy consumers and
                // direct SQL queries get a usable value.
                'path' => 'storage/uploads/' . $target,
                'attachment_type_id' => $this->resolveAttachmentTypeId(),
                'role' => $role,
                'title' => $title ?? Str::headline(pathinfo($target, PATHINFO_FILENAME)),
                'sort_order' => $sortOrder,
                'is_active' => true,
                'created_by' => $userId,
            ]
        );
    }

    /**
     * Resolve a sensible default AttachmentType id. The existing
     * AttachmentTypeSeeder names types by surface area (Trip, Destination,
     * Activity, …); rather than guess per-model, we pick the first available
     * row and cache it. Operators can rebind specific attachments to a
     * different type via the admin UI.
     */
    private function resolveAttachmentTypeId(): int
    {
        if ($this->cachedAttachmentTypeId !== null) {
            return $this->cachedAttachmentTypeId;
        }

        $id = AttachmentType::query()->orderBy('id')->value('id');
        if (!$id) {
            // Fall back to creating a generic type so seeding never blocks
            // on a missing AttachmentType row.
            $id = AttachmentType::create([
                'name' => 'Library Image',
                'color' => '#94a3b8',
            ])->id;
        }

        return $this->cachedAttachmentTypeId = (int) $id;
    }

    /**
     * Convenience: attach one cover image then any gallery images in order.
     *
     * @param array<int, string> $galleryTargets
     */
    public function attachCoverAndGallery(Model $model, string $cover, array $galleryTargets = []): void
    {
        $this->attach($model, $cover, Attachment::ROLE_COVER, 0);
        foreach (array_values($galleryTargets) as $i => $target) {
            $this->attach($model, $target, Attachment::ROLE_GALLERY, $i + 1);
        }
    }

    private function resolveEntry(array $entry): string
    {
        $base = match ($entry['from']) {
            self::SOURCE_FRONTEND => env('SBS_FRONTEND_ASSETS_PATH', base_path('../../serenbluesafaris/public/assets/images')),
            self::SOURCE_STORAGE => public_path('storage/uploads'),
            default => '',
        };

        $combined = rtrim($base, "/\\") . DIRECTORY_SEPARATOR . $entry['path'];

        // Normalize slashes + collapse ../ so Windows `is_file()` checks behave.
        $combined = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $combined);
        $resolved = realpath($combined);

        return $resolved !== false ? $resolved : $combined;
    }

    private function warn(string $message): void
    {
        if ($this->command) {
            $this->command->warn('  ' . $message);
        }
    }
}
