<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Request;

use App\Api\Exception\RequestMalformedException;
use App\Bank\Command\SendRequestToBank;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Резолвер сообщения отправки заявки в банк
 */
final class SendRequestToBankResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    private SerializerInterface $serializer;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return \is_a($argument->getType(), SendRequestToBank::class, true);
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var class-string<SendRequestToBank> $class
         */
        $class = $argument->getType();

        try {
            /** @var SendRequestToBank $requestObject */
            $requestObject = $this->serializer->deserialize($request->getContent(), $class, 'json');
        } catch (MissingConstructorArgumentsException $e) {
            throw new RequestMalformedException($e);
        }

        $errors = $this->validator->validate($requestObject);

        if ($errors->count() > 0) {
            throw new ValidationFailedException($requestObject, $errors);
        }

        yield $requestObject;
    }
}
