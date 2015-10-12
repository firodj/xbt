<?hh
namespace App\Console\Commands;

use Config;
use View;

class ViewCompile extends WoiCommand
{
    protected $signature = 'view:compile
                           {--dry-run : Kering}';

    public function process() {
        $views = array_filter(array_merge(Config::get('view.sites'), Config::get('view.paths')), function($path) {
            return is_string($path);
        });

        foreach ($views as $view_path) {
            $dir = new \RecursiveDirectoryIterator($view_path);
            $iterator = new \RecursiveIteratorIterator($dir);
            foreach ($iterator as $view) {
                if (!$view->isFile()) continue;
                $path = $view->getPathName();

                if ($this->option('dry-run')) {
                    $this->info($view->getPathName());
                } else {
                    try {
                        $engine = View::getEngineFromPath($path);
                        $engine->getCompiler()->compile($path);
                    } catch (\InvalidArgumentException $e) {
                        $this->info('SKIP: ' . $path . ' ERROR: ' . $e->getMessage());
                    } catch (\Exception $e) {
                        $this->error('FILE: ' . $path . ' ERROR: ' . $e->getMessage());
                    }
                }
            }
        }
    }

}
