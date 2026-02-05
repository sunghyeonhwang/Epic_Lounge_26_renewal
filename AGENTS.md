# Repository Guidelines

## Project Structure & Module Organization
- `contents/` contains public-facing PHP pages (events, news, replay, etc.).
- `inc/` and `resource/inc/` hold shared layout fragments (headers, footers, sidebars).
- `resource/` is the primary static asset tree: `css/`, `js/`, `images/`, plus versioned assets in `resource/24/` and `resource/25/`.
- `lib/` hosts PHP libraries (including bundled third-party code like `PHPExcel`).
- `adm/` contains admin and back-office tooling.
- `_common.php` and `common.php` glue together configuration and shared bootstrap logic.

## Build, Test, and Development Commands
- No formal build system or test runner is present in this repo.
- Local PHP server (basic smoke check): `php -S localhost:8000 -t .`
- Open a page directly (example): `http://localhost:8000/contents/event_list.php`

## Coding Style & Naming Conventions
- Follow existing PHP style: mixed tabs/spaces, inline PHP in templates, and short open tags (e.g., `<?` / `<?=`).
- Keep includes consistent with the current layout (e.g., `include '../inc/common_header.php';`).
- Asset paths are typically absolute from `/v3/` (e.g., `/v3/resource/css/sub.css`).
- Prefer descriptive, page-specific filenames in `contents/` (e.g., `event_list.php`, `news_view.php`).

## Testing Guidelines
- No automated tests are defined. Validate changes by loading the affected pages and checking console/network errors.
- When editing shared includes, spot-check a few pages that consume them.

## Commit & Pull Request Guidelines
- Git history is minimal (single “초기 커밋”); no established convention.
- Suggested commit format: short, descriptive summary in Korean or English (e.g., `Fix event list filters`).
- PRs should include: concise change summary, linked issue (if any), and screenshots for UI/CSS updates.

## Security & Configuration Tips
- This is a GNUBoard-based PHP site; database-backed pages expect the referenced tables to exist.
- Avoid committing credentials or environment-specific URLs; keep them in server-side config files.
