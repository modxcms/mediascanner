<?php
namespace MediaScanner\v2;

use MediaScanner\MediaFinder;

class Scanner
{
    /** @var \modX */
    private \modX $modx;
    private MediaFinder $mf;

    public function __construct(\modX &$modx)
    {
        $this->modx =& $modx;
        $this->mf = new MediaFinder($this->modx);
    }

    public function scan(\modResource $resource)
    {
        $isValid = $this->validateResource($resource);
        if (!$isValid) {
            return;
        }

        $this->clearResourceLinks($resource->id);
        $this->renderResource($resource);

        $this->mf->findMedia($this->modx->resource->_output, function($url) use ($resource) {
            $this->addMedia($url, $resource->id);
        });
    }

    private function validateResource(\modResource $resource)
    {
        if ($resource->ContentType->mime_type !== 'text/html') {
            return false;
        }
        if (in_array($resource->class_key, ['modWebLink', 'modSymLink'])) {
            return false;
        }

        return true;
    }

    private function clearResourceLinks($resourceId)
    {
        $this->modx->removeCollection('MediaScannerMediaResources', [
            'resource' => $resourceId
        ]);
    }

    private function renderResource(\modResource $resource)
    {
        require_once(MODX_CORE_PATH. '/model/modx/modrequest.class.php');
        $this->modx->switchContext($resource->context_key);
        $this->modx->resource = $resource;
        $this->modx->resourceIdentifier = $resource->id;
        $this->modx->elementCache = [];
        $this->modx->request = new \modRequest($this->modx, [], [], [], []);
        $this->modx->resource->prepare();
    }

    protected function addMedia($url, $resourceId)
    {
        $medium = $this->modx->getObject('MediaScannerMedia', ['url' => $url]);
        if (empty($medium)) {
            $medium = $this->modx->newObject('MediaScannerMedia');
            $medium->set('url', $url);
            $medium->save();
        }

        $mediaResource = $this->modx->getObject('MediaScannerMediaResources', ['resource' => $resourceId, 'medium' => $medium->id]);
        if (!empty($mediaResource)) {
            return;
        }

        $mediaResource = $this->modx->newObject('MediaScannerMediaResources');
        $mediaResource->set('resource', $resourceId);
        $mediaResource->set('medium', $medium->id);
        $mediaResource->save();
    }

    public static function purgeUnlinkedMedia(\modX $modx)
    {
        $c = $modx->newQuery('MediaScannerMedia');
        $c->select($modx->getSelectColumns('MediaScannerMedia', '', '', ['id']));
        $c->leftJoin('MediaScannerMediaResources', 'Resources');
        $c->where([
            'Resources.medium:IS' => null,
        ]);
        $c->prepare();
        $c->stmt->execute();
        $ids = $c->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

        if (!empty($ids)) {
            $modx->removeCollection('MediaScannerMedia', ['id:IN' => $ids]);
        }
    }
}