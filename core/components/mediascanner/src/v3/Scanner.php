<?php
namespace MediaScanner\v3;

use MediaScanner\MediaFinder;
use MediaScanner\v3\Model\Media;
use MediaScanner\v3\Model\MediaResources;
use MODX\Revolution\modResponse;
use MODX\Revolution\modResource;
use MODX\Revolution\modSymLink;
use MODX\Revolution\modWebLink;
use MODX\Revolution\modX;

class Scanner {
    /** @var modX */
    private modX $modx;
    private MediaFinder $mf;

    private array $skip = [];

    public function __construct(modX &$modx)
    {
        $this->modx =& $modx;
        $this->mf = new MediaFinder($this->modx);
        $skip = $this->modx->getOption('mediascanner.skip_scan');
        if (!empty($skip)) {
            $this->skip = explode(',', $skip);
        }
    }

    public function scan(modResource $resource)
    {
        $isValid = $this->validateResource($resource);
        if (!$isValid) return;

        $this->clearResourceLinks($resource->id);
        try {
            $this->renderResource($resource);
            $this->mf->findMedia($this->modx->resource->_output, function($url) use ($resource) {
                $this->addMedia($url, $resource->id);
            });
        } catch (\Exception $e) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Error rendering resource: ' . $resource->id);
        }
        $this->modx->config['modResponse.class'] = modResponse::class;
        $this->modx->response = new modResponse($this->modx);
    }

    private function validateResource(modResource $resource) {
        if (in_array($resource->id, $this->skip)) {
            return false;
        }
        if ($resource->ContentType->mime_type !== 'text/html') {
            return false;
        }
        if (in_array($resource->class_key, [modWebLink::class, modSymLink::class])) {
            return false;
        }

        return true;
    }

    private function clearResourceLinks($resourceId)
    {
        $this->modx->removeCollection(MediaResources::class, [
            'resource' => $resourceId
        ]);
    }

    private function renderResource(modResource $resource)
    {
        $this->modx->switchContext($resource->context_key);
        $this->modx->resource = $resource;
        $this->modx->resourceIdentifier = $resource->id;
        $this->modx->elementCache = [];
        $this->modx->config['modResponse.class'] = scanResponse::class;
        $this->modx->response = new scanResponse($this->modx);
        $this->modx->request = new scanRequest($this->modx, [], [], [], []);
        $this->modx->resource->prepare();
    }

    protected function addMedia($url, $resourceId)
    {
        $medium = $this->modx->getObject(Media::class, ['url' => $url]);
        if (empty($medium)) {
            $medium = $this->modx->newObject(Media::class);
            $medium->set('url', $url);
            $medium->save();
        }

        $mediaResource = $this->modx->getObject(MediaResources::class, ['resource' => $resourceId, 'medium' => $medium->id]);
        if (!empty($mediaResource)) {
            return;
        }

        $mediaResource = $this->modx->newObject(MediaResources::class);
        $mediaResource->set('resource', $resourceId);
        $mediaResource->set('medium', $medium->id);
        $mediaResource->save();
    }

    public static function purgeUnlinkedMedia(modX $modx)
    {
        $c = $modx->newQuery(Media::class);
        $c->select($modx->getSelectColumns(Media::class, '', '', ['id']));
        $c->leftJoin(MediaResources::class, 'Resources');
        $c->where([
            'Resources.medium:IS' => null,
        ]);
        $c->prepare();
        $c->stmt->execute();
        $ids = $c->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

        if (!empty($ids)) {
            $modx->removeCollection(Media::class, ['id:IN' => $ids]);
        }
    }
}