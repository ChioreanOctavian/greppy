<?php

namespace greppy\Controllers;

use greppy\Contracts\ControllerInterface;
use greppy\Entity\User;
use greppy\Hashing\HashingService;
use greppy\Http\Request;
use greppy\Http\Response;
use greppy\Http\Stream;
use greppy\Repository\UserRepository;

class UserController implements ControllerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var HashingService
     */
    private $hasingService;

    public function __construct(UserRepository $userRepository, HashingService $hashingService)
    {
        $this->userRepository = $userRepository;
        $this->hasingService = $hashingService;
    }

    /**
     * @param Request $request
     * @param array $requestAttributes
     * @return Response
     */
    public function createUser(Request $request, array $requestAttributes): Response
    {
        $userAttr = json_decode($request->getBody()->__toString());
        if ($this->userRepository->verifyEmail($userAttr->email)) {
            return new Response
            (
                Stream::createFromString(json_encode(["message" => "Email already exists"])),
                ["Content-Type" => "application/json"]
            );
        }

        $user = new User();
        $user->setEmail($userAttr->email);
        $user->setPassword($this->hasingService->encrypt($userAttr->password));

        if (!$this->userRepository->insertOnDuplicateKeyUpdate($user)) {
            return new Response(
                Stream::createFromString(json_encode(["message" => "User Not Created"])),
                ["Content-Type" => "application/json"]
            );
        }

        return new Response
        (
            Stream::createFromString(json_encode(["message" => "User Create Successfully"])),
            ["Content-Type" => "application/json"]
        );
    }

    /**
     * @param Request $request
     * @param array $requestAttributes
     * @return Response
     */
    public function authenticate(Request $request, array $requestAttributes)
    {
        $userAttr = json_decode($request->getBody()->__toString());

        $user = $this->userRepository->findOneBy(["email" => $userAttr->email]);
        if (!isset($user)) {
            return new Response
            (
                Stream::createFromString(json_encode(["message" => "User does not exists"])),
                ["Content-Type" => "application/json"]
            );
        }

        if (!$this->hasingService->checkPassword($userAttr->password, $user->getPassword())) {
            return new Response
            (
                Stream::createFromString(json_encode(["message" => "Incorrect password"])),
                ["Content-Type" => "application/json"]
            );
        }

        return new Response
        (
            Stream::createFromString(json_encode(["message" => "Authentication Successfully"])),
            ["Content-Type" => "application/json"]
        );
    }


}