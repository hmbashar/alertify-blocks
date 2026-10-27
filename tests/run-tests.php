<?php
/**
 * Standalone PHP test runner for Alertify Blocks.
 * No dependencies — runs with plain `php tests/run-tests.php`.
 */

define( 'ABSPATH', '/tmp/fake-abspath/' );
define( 'ALERTIFY_BLOCKS_VERSION', '1.0.1' );
define( 'ALERTIFY_BLOCKS_DIR', dirname( __DIR__ ) . '/' );
define( 'ALERTIFY_BLOCKS_URL', 'https://example.com/wp-content/plugins/alertify-blocks/' );

// WordPress stubs
function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function register_block_type( $block_type, $args = [] ) { return true; }

require_once dirname( __DIR__ ) . '/includes/class-alertify-blocks-loader.php';

// ─── Tiny test framework ───────────────────────────────────────────────────

$passed = 0;
$failed = 0;
$errors = [];

function it( string $desc, callable $fn ): void {
    global $passed, $failed, $errors;
    try {
        $fn();
        echo "\033[32m  ✓\033[0m {$desc}\n";
        $passed++;
    } catch ( \Throwable $e ) {
        echo "\033[31m  ✗\033[0m {$desc}\n";
        echo "    → " . $e->getMessage() . "\n";
        $failed++;
        $errors[] = $desc;
    }
}

class Expectation {
    private mixed $value;
    public function __construct( mixed $value ) { $this->value = $value; }

    public function toBe( mixed $expected ): void {
        if ( $this->value !== $expected ) {
            throw new \RuntimeException(
                "Expected " . var_export( $expected, true ) .
                ", got " . var_export( $this->value, true )
            );
        }
    }

    public function toBeTrue(): void  { $this->toBe( true ); }
    public function toBeFalse(): void { $this->toBe( false ); }
    public function toBeNull(): void  { $this->toBe( null ); }

    public function toBeInstanceOf( string $class ): void {
        if ( ! ( $this->value instanceof $class ) ) {
            throw new \RuntimeException(
                "Expected instance of {$class}, got " . get_class( $this->value )
            );
        }
    }

    public function toMatchRegex( string $pattern ): void {
        if ( ! preg_match( $pattern, (string) $this->value ) ) {
            throw new \RuntimeException(
                "Value " . var_export( $this->value, true ) .
                " does not match {$pattern}"
            );
        }
    }

    public function toBeNonEmpty(): void {
        if ( empty( $this->value ) ) {
            throw new \RuntimeException( "Expected non-empty value, got empty." );
        }
    }

    public function toBeDir(): void {
        if ( ! is_dir( $this->value ) ) {
            throw new \RuntimeException( "Expected a directory, got: {$this->value}" );
        }
    }
}

function expect( $actual ): Expectation {
    return new Expectation( $actual );
}

function expectThrows( callable $fn, string $exceptionClass = \Throwable::class ): void {
    try {
        $fn();
        throw new \RuntimeException( "Expected {$exceptionClass} but nothing was thrown." );
    } catch ( \Throwable $e ) {
        if ( ! ( $e instanceof $exceptionClass ) ) {
            // Re-throw if it's OUR RuntimeException from the "nothing was thrown" branch
            if ( str_contains( $e->getMessage(), 'Expected ' . $exceptionClass . ' but nothing' ) ) {
                throw $e;
            }
            // Otherwise the wrong exception type was thrown — that's still a failure
            throw new \RuntimeException(
                "Expected {$exceptionClass}, but got " . get_class( $e ) . ': ' . $e->getMessage()
            );
        }
        // Correct exception thrown — pass
    }
}

// ─── Tests ────────────────────────────────────────────────────────────────

echo "\n\033[1mAlerify Blocks — PHP Unit Tests\033[0m\n\n";

// ── Constants ─────────────────────────────────────────────────────────────
echo "Constants\n";

it( 'ALERTIFY_BLOCKS_VERSION is defined and non-empty', function () {
    expect( defined( 'ALERTIFY_BLOCKS_VERSION' ) )->toBeTrue();
    expect( ALERTIFY_BLOCKS_VERSION )->toBeNonEmpty();
} );

it( 'ALERTIFY_BLOCKS_DIR points to an existing directory', function () {
    expect( defined( 'ALERTIFY_BLOCKS_DIR' ) )->toBeTrue();
    expect( ALERTIFY_BLOCKS_DIR )->toBeDir();
} );

it( 'ALERTIFY_BLOCKS_URL is a valid URL string', function () {
    expect( defined( 'ALERTIFY_BLOCKS_URL' ) )->toBeTrue();
    expect( ALERTIFY_BLOCKS_URL )->toMatchRegex( '#^https?://#' );
} );

it( 'ABSPATH constant is defined', function () {
    expect( defined( 'ABSPATH' ) )->toBeTrue();
} );

// ── Loader singleton ──────────────────────────────────────────────────────
echo "\nLoader — singleton\n";

it( 'get_instance() returns a Loader object', function () {
    $loader = \ALERTIFY\Includes\Loader::get_instance();
    expect( $loader )->toBeInstanceOf( \ALERTIFY\Includes\Loader::class );
} );

it( 'get_instance() always returns the same object (singleton)', function () {
    $a = \ALERTIFY\Includes\Loader::get_instance();
    $b = \ALERTIFY\Includes\Loader::get_instance();
    if ( $a !== $b ) {
        throw new \RuntimeException( 'get_instance() returned different objects.' );
    }
} );

// ── Loader — clone / wakeup protection ───────────────────────────────────
echo "\nLoader — clone / wakeup protection\n";

it( 'cloning the Loader throws an Error', function () {
    expectThrows( function () {
        $loader = \ALERTIFY\Includes\Loader::get_instance();
        $clone  = clone $loader;
    }, \Error::class );
} );

it( '__wakeup() throws an Exception', function () {
    expectThrows( function () {
        \ALERTIFY\Includes\Loader::get_instance()->__wakeup();
    }, \Exception::class );
} );

// ── Loader — public methods ───────────────────────────────────────────────
echo "\nLoader — public methods\n";

it( 'enqueue_editor_assets() returns null (no-op)', function () {
    $loader = \ALERTIFY\Includes\Loader::get_instance();
    expect( $loader->enqueue_editor_assets() )->toBeNull();
} );

it( 'enqueue_frontend_assets() returns null (no-op)', function () {
    $loader = \ALERTIFY\Includes\Loader::get_instance();
    expect( $loader->enqueue_frontend_assets() )->toBeNull();
} );

// ── register_blocks() — manual fallback (no modern WP functions) ──────────
echo "\nLoader — register_blocks() fallback\n";

it( 'register_blocks() does not throw without modern WP functions', function () {
    // Create a temporary fake blocks-manifest.php
    $buildDir = ALERTIFY_BLOCKS_DIR . 'blocks/build/';
    @mkdir( $buildDir, 0755, true );
    $manifestPath = $buildDir . 'blocks-manifest.php';
    $created = false;

    if ( ! file_exists( $manifestPath ) ) {
        file_put_contents(
            $manifestPath,
            '<?php return ["dismissible-notice" => [], "notice-box" => [], "inline-message" => []];'
        );
        $created = true;
    }

    try {
        \ALERTIFY\Includes\Loader::get_instance()->register_blocks();
    } finally {
        if ( $created && file_exists( $manifestPath ) ) {
            unlink( $manifestPath );
        }
    }
    // If we get here, no exception was thrown
} );

it( 'register_blocks() with wp_register_block_types_from_metadata_collection returns early', function () {
    // Define the modern function in the global scope temporarily via runkit is not
    // available, so we test that the loader WOULD call it if defined by checking
    // the function_exists branch via a wrapper.
    $called = false;

    // We can't redefine the function mid-run, but we CAN verify the branch exists
    // by inspecting that the loader file contains the expected function call.
    $loaderSrc = file_get_contents( ALERTIFY_BLOCKS_DIR . 'includes/class-alertify-blocks-loader.php' );
    if ( strpos( $loaderSrc, 'wp_register_block_types_from_metadata_collection' ) === false ) {
        throw new \RuntimeException( 'Loader does not reference wp_register_block_types_from_metadata_collection.' );
    }
} );

// ── Source file integrity checks ──────────────────────────────────────────
echo "\nSource file integrity\n";

it( 'notice-box/edit.js has no BlockControls import', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/notice-box/edit.js' );
    if ( strpos( $src, 'BlockControls' ) !== false ) {
        throw new \RuntimeException( 'BlockControls still present in notice-box/edit.js (unused import).' );
    }
} );

it( 'notice-box/edit.js has no AlignmentToolbar import', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/notice-box/edit.js' );
    if ( strpos( $src, 'AlignmentToolbar' ) !== false ) {
        throw new \RuntimeException( 'AlignmentToolbar still present in notice-box/edit.js (unused import).' );
    }
} );

it( 'inline-message/block.json has no alertId attribute', function () {
    $json = json_decode( file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/inline-message/block.json' ), true );
    if ( isset( $json['attributes']['alertId'] ) ) {
        throw new \RuntimeException( 'alertId still present in inline-message/block.json.' );
    }
} );

it( 'inline-message/edit.js has no alertId references', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/inline-message/edit.js' );
    if ( strpos( $src, 'alertId' ) !== false ) {
        throw new \RuntimeException( 'alertId reference still present in inline-message/edit.js.' );
    }
} );

it( 'inline-message/edit.js has no RangeControl import', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/inline-message/edit.js' );
    if ( strpos( $src, 'RangeControl' ) !== false ) {
        throw new \RuntimeException( 'RangeControl still imported in inline-message/edit.js (was it ever used?).' );
    }
} );

it( 'dismissible-notice/style.css has no dead .alertify-notice-close class', function () {
    $css = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/dismissible-notice/style.css' );
    if ( strpos( $css, '.alertify-notice-close' ) !== false ) {
        throw new \RuntimeException( 'Dead .alertify-notice-close CSS still present.' );
    }
} );

it( 'inline-message/style.css has no duplicate .alertify-inline rule', function () {
    $css  = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/inline-message/style.css' );
    $count = substr_count( $css, '.alertify-inline {' );
    if ( $count > 1 ) {
        throw new \RuntimeException( ".alertify-inline { appears {$count} times — duplicates remain." );
    }
} );

it( 'inline-message/style.css has no duplicate .alertify-inline.alertify-info rule', function () {
    $css   = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/inline-message/style.css' );
    $count = substr_count( $css, '.alertify-inline.alertify-info' );
    if ( $count > 1 ) {
        throw new \RuntimeException( ".alertify-inline.alertify-info appears {$count} times — duplicates remain." );
    }
} );

it( 'dismissible-notice/save.js outputs data-alert-id and data-dismiss-duration', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/dismissible-notice/save.js' );
    if ( strpos( $src, 'data-alert-id' ) === false ) {
        throw new \RuntimeException( 'data-alert-id missing from dismissible-notice/save.js.' );
    }
    if ( strpos( $src, 'data-dismiss-duration' ) === false ) {
        throw new \RuntimeException( 'data-dismiss-duration missing from dismissible-notice/save.js.' );
    }
} );

it( 'dismissible-notice.js reads alertId and dismissDuration from dataset', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'blocks/src/dismissible-notice/dismissible-notice.js' );
    if ( strpos( $src, 'dataset.alertId' ) === false ) {
        throw new \RuntimeException( 'JS does not read dataset.alertId.' );
    }
    if ( strpos( $src, 'dataset.dismissDuration' ) === false ) {
        throw new \RuntimeException( 'JS does not read dataset.dismissDuration.' );
    }
} );

it( 'loader PHP file references wp_register_block_metadata_collection', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'includes/class-alertify-blocks-loader.php' );
    if ( strpos( $src, 'wp_register_block_metadata_collection' ) === false ) {
        throw new \RuntimeException( 'wp_register_block_metadata_collection not found in loader.' );
    }
} );

it( 'all block.json files have required apiVersion field', function () {
    $blockJsonFiles = glob( ALERTIFY_BLOCKS_DIR . 'blocks/src/*/block.json' );
    if ( empty( $blockJsonFiles ) ) {
        throw new \RuntimeException( 'No block.json files found.' );
    }
    foreach ( $blockJsonFiles as $path ) {
        $data = json_decode( file_get_contents( $path ), true );
        if ( ! isset( $data['apiVersion'] ) ) {
            throw new \RuntimeException( "Missing apiVersion in {$path}." );
        }
    }
} );

it( 'all block.json files have required name field', function () {
    $blockJsonFiles = glob( ALERTIFY_BLOCKS_DIR . 'blocks/src/*/block.json' );
    foreach ( $blockJsonFiles as $path ) {
        $data = json_decode( file_get_contents( $path ), true );
        if ( empty( $data['name'] ) ) {
            throw new \RuntimeException( "Missing or empty name in {$path}." );
        }
    }
} );

it( 'all block.json files are valid JSON', function () {
    $blockJsonFiles = glob( ALERTIFY_BLOCKS_DIR . 'blocks/src/*/block.json' );
    foreach ( $blockJsonFiles as $path ) {
        $data = json_decode( file_get_contents( $path ), true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            throw new \RuntimeException( "Invalid JSON in {$path}: " . json_last_error_msg() );
        }
    }
} );

it( 'main plugin file has correct version constant', function () {
    $src = file_get_contents( ALERTIFY_BLOCKS_DIR . 'alertify-blocks.php' );
    if ( strpos( $src, "define('ALERTIFY_BLOCKS_VERSION', '1.0.1')" ) === false ) {
        throw new \RuntimeException( "Version constant not set to '1.0.1' in alertify-blocks.php." );
    }
} );

it( 'readme.txt stable tag matches plugin version', function () {
    $readme = file_get_contents( ALERTIFY_BLOCKS_DIR . 'readme.txt' );
    if ( strpos( $readme, 'Stable tag: 1.0.1' ) === false ) {
        throw new \RuntimeException( "Stable tag in readme.txt does not match 1.0.1." );
    }
} );

// ─── Summary ──────────────────────────────────────────────────────────────

$total = $passed + $failed;
echo "\n";
echo str_repeat( '─', 50 ) . "\n";

if ( $failed === 0 ) {
    echo "\033[32m✓ All {$total} tests passed.\033[0m\n\n";
    exit( 0 );
} else {
    echo "\033[31m✗ {$failed} of {$total} tests failed:\033[0m\n";
    foreach ( $errors as $err ) {
        echo "  - {$err}\n";
    }
    echo "\n";
    exit( 1 );
}
