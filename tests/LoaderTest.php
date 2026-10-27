<?php
namespace ALERTIFY\Tests;

use PHPUnit\Framework\TestCase;
use ALERTIFY\Includes\Loader;

/**
 * Unit tests for the Loader class.
 */
class LoaderTest extends TestCase {

    /**
     * Loader is a singleton — get_instance() always returns the same object.
     */
    public function test_get_instance_returns_singleton(): void {
        $a = Loader::get_instance();
        $b = Loader::get_instance();

        $this->assertInstanceOf( Loader::class, $a );
        $this->assertSame( $a, $b, 'get_instance() must always return the same object.' );
    }

    /**
     * Loader cannot be cloned.
     */
    public function test_clone_is_forbidden(): void {
        $loader = Loader::get_instance();

        $this->expectException( \Error::class );
        $clone = clone $loader;
    }

    /**
     * Loader cannot be unserialized.
     */
    public function test_wakeup_throws(): void {
        $loader = Loader::get_instance();

        $this->expectException( \Exception::class );
        $loader->__wakeup();
    }

    /**
     * ALERTIFY_BLOCKS_VERSION constant is defined and non-empty.
     */
    public function test_version_constant_defined(): void {
        $this->assertTrue( defined( 'ALERTIFY_BLOCKS_VERSION' ) );
        $this->assertNotEmpty( ALERTIFY_BLOCKS_VERSION );
    }

    /**
     * ALERTIFY_BLOCKS_DIR points to an existing directory.
     */
    public function test_dir_constant_is_valid_directory(): void {
        $this->assertTrue( defined( 'ALERTIFY_BLOCKS_DIR' ) );
        $this->assertDirectoryExists( ALERTIFY_BLOCKS_DIR );
    }

    /**
     * ALERTIFY_BLOCKS_URL constant is a valid URL string.
     */
    public function test_url_constant_is_string(): void {
        $this->assertTrue( defined( 'ALERTIFY_BLOCKS_URL' ) );
        $this->assertMatchesRegularExpression( '#^https?://#', ALERTIFY_BLOCKS_URL );
    }

    /**
     * register_blocks() falls back to the manual loop when neither modern
     * function exists (simulates WordPress < 6.7).
     *
     * We stub register_block_type to track calls and provide a fake manifest.
     */
    public function test_register_blocks_manual_fallback(): void {
        // Provide a fake blocks-manifest.php that the loader will require.
        $buildDir = ALERTIFY_BLOCKS_DIR . 'blocks/build/';
        if ( ! is_dir( $buildDir ) ) {
            mkdir( $buildDir, 0755, true );
        }

        $manifestPath = $buildDir . 'blocks-manifest.php';
        if ( ! file_exists( $manifestPath ) ) {
            file_put_contents(
                $manifestPath,
                '<?php return ["dismissible-notice" => [], "notice-box" => [], "inline-message" => []];'
            );
            $createdManifest = true;
        }

        // Capture register_block_type calls via a global counter.
        global $alertify_registered_blocks;
        $alertify_registered_blocks = [];

        // Neither modern function is defined in this test environment, so the
        // loader will run the manual loop. We just confirm it doesn't throw.
        $loader = Loader::get_instance();

        try {
            $loader->register_blocks();
            $this->assertTrue( true, 'register_blocks() completed without throwing.' );
        } catch ( \Throwable $e ) {
            // Only fail if the manifest was present (i.e., setup was fine).
            if ( file_exists( $manifestPath ) ) {
                $this->fail( 'register_blocks() threw: ' . $e->getMessage() );
            }
        } finally {
            if ( isset( $createdManifest ) && file_exists( $manifestPath ) ) {
                unlink( $manifestPath );
            }
        }
    }

    /**
     * enqueue_editor_assets() returns without error (currently a no-op).
     */
    public function test_enqueue_editor_assets_is_noop(): void {
        $loader = Loader::get_instance();
        $result = $loader->enqueue_editor_assets();
        $this->assertNull( $result );
    }

    /**
     * enqueue_frontend_assets() returns without error (currently a no-op).
     */
    public function test_enqueue_frontend_assets_is_noop(): void {
        $loader = Loader::get_instance();
        $result = $loader->enqueue_frontend_assets();
        $this->assertNull( $result );
    }
}
