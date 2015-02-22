<?php

namespace Pheat\Feature;

use Pheat\ContextInterface;
use Pheat\Status;

class RatioFeature extends Feature
{
    /**
     * Unsigned short, 2 ^ 16
     */
    const RESOLUTION = 65535;

    /**
     * Enabled ratio to maintain, expressed as a float
     *
     * 0.0 <= ratio <= 1.0
     *
     * @var float
     */
    protected $ratio;

    /**
     * A key in the context to vary against
     *
     * @var string|false
     */
    protected $vary      = false;

    /**
     * The resolved value that will be used to vary the ratio
     *
     * Must be able to be cast to string
     *
     * @var string
     */
    protected $varyValue = null;

    /**
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);

        $this->ratio     = isset($configuration['ratio']) ? floatval($configuration['ratio']) : 0.0;
        $this->vary      = isset($configuration['vary']) ? $configuration['vary'] : null;
        $this->varyValue = (string)mt_rand(0, self::RESOLUTION); // default: if never supplied a context, unseeded ratio
    }

    /**
     * @return array<mixed>
     */
    public function getConfiguration()
    {
        return array_merge(parent::getConfiguration(), [
            'ratio' => $this->ratio,
            'vary'  => $this->vary
        ]);
    }

    /**
     * We assume pack format specifier 'I' is equal in size to PHP_INT_SIZE
     *
     * @param ContextInterface $context
     */
    public function context(ContextInterface $context)
    {
        if (!empty($this->vary)) {
            $this->varyValue = $context->get($this->vary);
        }
    }

    /**
     * Gets whether the feature is enabled according to the ratio alone
     *
     * A RatioFeature can be enabled and disabled like any other feature. Only when it
     * is enabled, with a ratio above 0, does the ratio actually come into play.
     *
     * We assume 0 <= $this->varyValue <= self::RESOLUTION when entering, uniformly
     * distributed.
     *
     * @return boolean|null
     */
    protected function getRatioStatus()
    {
        if ($this->ratio == 0.0) {
            return Status::INACTIVE;
        }

        if ($this->ratio >= 1.0) {
            return Status::ACTIVE;
        }

        $value = unpack('nint', md5((string)$this->varyValue, true))['int'];
        $filter = (int)floor(self::RESOLUTION * (float)$this->ratio);

        return ($value < $filter);
    }

    /**
     * @return bool|null see Status
     */
    public function getStatus()
    {
        $status = parent::getStatus();

        if (empty($status)) {
            return $status;
        }

        $ratio = $this->getRatioStatus();

        return $ratio;
    }
}
